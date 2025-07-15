<?php

use App\Jobs\DisconnectedDeviceIncidentMailSender;
use App\Jobs\IncidentMailSender;
use App\Mail\IncidentMailer;
use App\Mail\TicketMail;
use App\Models\Auditlogs;
use App\Models\Batches;
use App\Models\Clientdetails;
use App\Models\Clients;
use App\Models\Clientstats;
use App\Models\Customers;
use App\Models\Devices;
use App\Models\Lognotifications;
use App\Models\Logs;
use App\Models\Overviewdiagrams;
use App\Models\Sites;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use Smark\Smark\Dater;
use Smark\Smark\JSON;

// latest access token

function latestAccessToken()
{
    return JSON::jsonRead('public/accessTokenStorage/accessTokens.json')[0]['accessToken'];
}


function latestBatch()
{
    return Batches::latest('id')->first();
}

function createNewAPITokenAlgo()
{
    $postRequestForNewApiKey = Http::withHeaders([
        'Content-Type'      => 'application/json',  // Optional, can be inferred from the `json` method
    ])->withOptions([
        'verify'            => false,
    ])->post(env('OMADAC_SERVER').'/openapi/authorize/token?grant_type=client_credentials', [
        'omadacId'          => env('OMADAC_ID'),
        'client_id'         => env('CLIENT_ID'),
        'client_secret'     => env('CLIENT_SECRET'),
    ]);

    // Decode the response body from JSON to an array
    $responseBody = json_decode($postRequestForNewApiKey->body(), true);  // Decode into an associative array

    return JSON::jsonUnshift('public/accessTokenStorage/accessTokens.json', $responseBody['result']);
}

function getTrafficData($siteId, $start, $end)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer AccessToken=' . latestAccessToken(),
    ])->withOptions([
        'verify'        => false,
    ])->get(
        env('OMADAC_SERVER') .
        '/openapi/v1/' . env('OMADAC_ID') .
        '/sites/' . $siteId .
        '/dashboard/traffic-activities?start=' . $start . '&end=' . $end
    );

    return $response;
}

function latestAccessTokenCheckerError()
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer AccessToken='.latestAccessToken(), // Replace with your API key
    ])->withOptions([
        'verify' => false,
    ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites?page=1&pageSize=1000');

    return $response['errorCode'];

}

// incident mailer

function incidentMailerSender()
{
    $client = new \GuzzleHttp\Client();
    $omadacId = env('OMADAC_ID');

    $sites = [];

    // to get all site id
    foreach (Sites::all() as $key => $value) {
        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$value['siteId']}/devices?page=1&pageSize=1000", [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer AccessToken=' . latestAccessToken(), // Use appropriate token source
                ]
            ]);

            $sites[] = [
                'site' => Sites::where('siteId', $value['siteId'])->value('name'),
                'siteId' => $value['siteId'],
                'data' => json_decode($res->getBody(), true)
            ];
        } catch (\Exception $e) {
            return response()->json([
                'errorCode' => 1,
                'message' => 'Failed to fetch device data.',
                'error' => $e->getMessage()
            ]);
        }
    }

    $FINAL_OFFLINE_DEVICES = [];

    foreach ($sites as $key => $site) {

        foreach ($site['data']['result']['data'] as $key => $device) {

            if ($device['status'] === 0) {
                $FINAL_OFFLINE_DEVICES[] = [
                    'site'      => $site['site'],
                    'siteId'    => $site['siteId'],
                    'device'    => [
                        'name'  => $device['name'],
                        'mac'   => $device['mac'],
                        'type'  => $device['type'],
                    ],
                    'status'    => 'offline'
                ];
            }

        }

    }

    // detect traffic =======================================

    $trafficData = [];

    $timezone = new DateTimeZone('Asia/Manila');

    $now = new DateTime('now', $timezone); // current date and time in Manila
    $todayStart = new DateTime($now->format('Y-m-d') . ' 00:00:00', $timezone);

    $startUnix = $todayStart->getTimestamp(); // midnight timestamp
    $endUnix = $now->getTimestamp();          // current timestamp

    // to get all site id
    foreach (Sites::all() as $key => $site) {

        $switchTrafficActivities = json_decode(getTrafficData($site['siteId'], $startUnix, $endUnix)->body(), true)['result']['switchTrafficActivities'];

        $trafficData[] = [
            'name'      => $site['name'],
            'siteId'    => $site['siteId'],
            'main_data' => $switchTrafficActivities,
        ];
    }

    $FINAL_LIST_OF_OFFLINE_SITES = [];

    foreach ($trafficData as $site) {
        $latestEntry = null;

        foreach ($site['main_data'] as $entry) {
            if (
                isset($entry['txData'], $entry['dxData'], $entry['time']) &&
                $entry['txData'] === 0.0 &&
                $entry['dxData'] === 0.0
            ) {
                // Compare times
                if (!$latestEntry || $entry['time'] > $latestEntry['time']) {
                    $latestEntry = $entry;
                }
            }
        }

        if ($latestEntry) {
            $FINAL_LIST_OF_OFFLINE_SITES[] = [
                'name'      => $site['name'],
                'siteId'    => $site['siteId'],
                'time'      => (new DateTime("@{$latestEntry['time']}"))
                    ->setTimezone(new DateTimeZone('Asia/Manila'))
                    ->format('F j, Y \a\t g:i A'),
            ];
        }
    }

    // $FINAL_LIST_OF_OFFLINE_SITES
    // $FINAL_OFFLINE_DEVICES

    IncidentMailSender::dispatch($FINAL_LIST_OF_OFFLINE_SITES);
    DisconnectedDeviceIncidentMailSender::dispatch($FINAL_OFFLINE_DEVICES);

    return "Offline Notification Sent!";
}

Artisan::command('sync-sites {batchNumber}', function ($batchNumber) {
    // ✅ Closure that captures $batchNumber
    $querySites = function ($accessToken) use ($batchNumber) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken=' . $accessToken,
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER') . '/openapi/v1/' . env('OMADAC_ID') . '/sites?page=1&pageSize=1000');

        Sites::truncate();

        foreach ($response['result']['data'] ?? [] as $value) {
            Sites::create([
                'name'         => $value['name'] ?? null,
                'siteId'       => $value['siteId'] ?? null,
                'region'       => $value['region'] ?? null,
                'timezone'     => $value['timeZone'] ?? null,
                'scenario'     => $value['scenario'] ?? null,
                'type'         => $value['type'] ?? null,
                'supportES'    => $value['supportES'] ?? null,
                'supportL2'    => $value['supportL2'] ?? null,
                'batch_number' => $batchNumber, // ✅ Will now be saved correctly
            ]);
        }
    };

    // ✅ Proper token handling
    if (latestAccessTokenCheckerError() === 0) {
        $querySites(latestAccessToken());
        Logs::create([
            // 'log' => 'Sites synced on ' . Dater::humanReadableDateWithDayAndTime(now()),
            'log' => 'The database has been synced on ' . Dater::humanReadableDateWithDayAndTime(now()),
        ]);
    } else {
        createNewAPITokenAlgo();
        $querySites(latestAccessToken());
        Logs::create([
            // 'log' => 'Sites synced with new token on ' . Dater::humanReadableDateWithDayAndTime(now()),
            'log' => 'The database has been synced with new token on ' . Dater::humanReadableDateWithDayAndTime(now()),
        ]);
    }

    // $this->info("Sites synced with batch: $batchNumber");
    $this->info("The database has been synced with batch: $batchNumber");
});



Artisan::command('sync-audit-logs {batchNumber}', function ($batchNumber) {

    function queryAuditLogsDataFromTheDatabaseFromHTTP($latestAccessTokenParam, $batchNumber)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/audit-logs?page=1&pageSize=1000');

        Auditlogs::truncate();

        foreach ($response['result']['data'] as $key => $value) {
            Auditlogs::create([
                'time'      => isset($value['time']) ? $value['time'] : null,
                'operator'  => isset($value['operator']) ? $value['operator'] : null,
                'resource'  => isset($value['resource']) ? $value['resource'] : null,
                'ip'        => isset($value['ip']) ? $value['ip'] : null,
                'auditType' => isset($value['auditType']) ? $value['auditType'] : null,
                'level'     => isset($value['level']) ? $value['level'] : null,
                'result'    => isset($value['result']) ? $value['result'] : null,
                'content'   => isset($value['content']) ? $value['content'] : null,
                'content'   => isset($value['content']) ? $value['content'] : null,
                'label'     => isset($value['label']) ? $value['label'] : null,
                'oldValue'  => isset($value['oldValue']) ? $value['oldValue'] : null,
                'newValue'  => isset($value['newValue']) ? $value['newValue'] : null,
                'batch_number'      => $batchNumber,
            ]);
        }
    }

    if (latestAccessTokenCheckerError() === 0) {

        queryAuditLogsDataFromTheDatabaseFromHTTP(latestAccessToken(), $batchNumber);

    } else {

        // if expired

        createNewAPITokenAlgo();
        queryAuditLogsDataFromTheDatabaseFromHTTP(latestAccessToken(), $batchNumber);
    }

    return $this->info('Audit Logs has been sync;');
});

Artisan::command('sync-devices {batchNumber}', function ($batchNumber) {

    function syncDevices($batchNumber)
    {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $sites = Sites::all();
        $devices = [];

        Devices::truncate();

        foreach ($sites as $key => $site) {
            try {
                $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$site['siteId']}/devices?page=1&pageSize=1000", [
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Bearer AccessToken=' . latestAccessToken(), // Use appropriate token source
                    ]
                ]);

                $response = json_decode($res->getBody(), true);

                foreach ($response['result']['data'] as $key => $device) {
                    Devices::create([
                        'device_name'   => $device['name'] ?? 'N/A',
                        'ip_address'    => $device['ip'] ?? 'N/A',
                        'status'        => $device['status'],
                        'model'         => $device['model'] ?? 'N/A',
                        'version'       => $device['firmwareVersion'] ?? 'N/A',
                        'uptime'        => $device['uptime'] ?? 'N/A',
                        'cpu'           => $device['cpuUtil'] ?? 'N/A',
                        'memory'        => $device['memUtil'] ?? 'N/A',
                        'public_ip'     => $device['publicIp'] ?? 'N/A',
                        'link_speed'    => $device['linkSpeed'] ?? 'N/A',
                        'duplex'        => $device['duplex'] ?? 'N/A',
                        'siteId'        => $site['siteId'],
                        'batch_number'        => $batchNumber,
                    ]);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'errorCode' => 1,
                    'message' => 'Failed to fetch device data.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $devices;
    }

    if (latestAccessTokenCheckerError() === 0) {
         print_r(syncDevices($batchNumber));
    } else {
        // if expired
        createNewAPITokenAlgo();
        print_r(syncDevices($batchNumber));
    }
    return $this->info('Devices has been sync;');
});

Artisan::command('sync-clients {batchNumber}', function ($batchNumber) {

    function syncClients($batchNumber)
    {
        $devices = [];

        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $sites = Sites::all();

        Clients::truncate();
        Clientstats::truncate();

        foreach ($sites as $key => $site) {

            try {
                $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$site['siteId']}/clients?page=1&pageSize=1000", [
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Bearer AccessToken=' . latestAccessToken(),
                    ]
                ]);

                $response = json_decode($res->getBody(), true);

                // insert to clients table

                foreach ($response['result']['data'] as $key => $device) {
                    Clients::create([
                        'mac_address'           => $device['mac'] ?? 'N/A',
                        'device_name'           => $device['name'] ?? 'N/A',
                        'device_type'           => $device['deviceType'] ?? 'N/A',
                        'connected_device_type' => $device['connectDevType'] ?? 'N/A',
                        'switch_name'           => $device['switchName'] ?? 'N/A',
                        'port'                  => $device['port'] ?? 'N/A',
                        'standard_port'         => $device['standardPort'] ?? 'N/A',
                        'network_theme'         => $device['networkName'] ?? 'N/A',
                        'uptime'                => $device['uptime'] ?? 'N/A',
                        'traffic_down'          => $device['trafficDown'] ?? 'N/A',
                        'traffic_up'            => $device['trafficUp'] ?? 'N/A',
                        'status'                => $device['active'] ?? 'N/A',
                        'siteId'                => $site['siteId'],
                        'batch_number'        => $batchNumber,
                    ]);
                }

                // insert to client stat table

                Clientstats::create([
                    "total"             => $response['result']['clientStat']['total'],
                    "wireless"          => $response['result']['clientStat']['wireless'],
                    "wired"             => $response['result']['clientStat']['wired'],
                    "num2g"             => $response['result']['clientStat']['num2g'],
                    "num5g"             => $response['result']['clientStat']['num5g'],
                    "num6g"             => $response['result']['clientStat']['num6g'],
                    "numUser"           => $response['result']['clientStat']['numUser'],
                    "numGuest"          => $response['result']['clientStat']['numGuest'],
                    "numWirelessUser"   => $response['result']['clientStat']['numWirelessUser'],
                    "numWirelessGuest"  => $response['result']['clientStat']['numWirelessGuest'],
                    "num2gUser"         => $response['result']['clientStat']['num2gUser'],
                    "num5gUser"         => $response['result']['clientStat']['num5gUser'],
                    "num6gUser"         => $response['result']['clientStat']['num6gUser'],
                    "num2gGuest"        => $response['result']['clientStat']['num2gGuest'],
                    "num5gGuest"        => $response['result']['clientStat']['num5gGuest'],
                    "num6gGuest"        => $response['result']['clientStat']['num6gGuest'],
                    "poor"              => $response['result']['clientStat']['poor'],
                    "fair"              => $response['result']['clientStat']['fair'],
                    "noData"            => $response['result']['clientStat']['noData'],
                    "good"              => $response['result']['clientStat']['good'],
                    "siteId"            => $site['siteId'],
                    'batch_number'        => $batchNumber,
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'errorCode' => 1,
                    'message' => 'Failed to fetch client data.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $devices;
    }

    if (latestAccessTokenCheckerError() === 0) {
         print_r(syncClients($batchNumber));
    } else {
        // if expired
        createNewAPITokenAlgo();
        print_r(syncClients($batchNumber));
    }
    return $this->info('Clients has been sync;');
});

Artisan::command('sync-client-details {batchNumber}', function ($batchNumber) {

    function syncClientDetails($batchNumber)
    {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $sites = Sites::all();
        $syncedClients = [];

        Clientdetails::truncate();

        foreach ($sites as $site) {
            $siteId = $site['siteId']; // adjust if your column is named differently

            // Get clients for this site (you may filter this from DB if there's a relationship)
            $clients = Clients::where('siteId', $siteId)->get();

            foreach ($clients as $clnt) {
                $macAddress = $clnt['mac_address']; // adjust if your column is named differently

                try {
                    $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/clients/{$macAddress}", [
                        'verify' => false,
                        'headers' => [
                            'Authorization' => 'Bearer AccessToken=' . latestAccessToken(),
                        ]
                    ]);

                    $response = json_decode($res->getBody(), true);

                    Clientdetails::create([
                        'mac'           => $response['result']['mac'] ?? null,
                        'name'          => $response['result']['name'] ?? null,
                        'deviceType'    => $response['result']['deviceType'] ?? null,
                        'switchName'    => $response['result']['switchName'] ?? null,
                        'switchMac'     => $response['result']['switchMac'] ?? null,
                        'port'          => $response['result']['port'] ?? null,
                        'standardPort'  => $response['result']['standardPort'] ?? null,
                        'trafficDown'   => $response['result']['trafficDown'] ?? null,
                        'trafficUp'     => $response['result']['trafficUp'] ?? null,
                        'uptime'        => $response['result']['uptime'] ?? null,
                        'guest'         => $response['result']['guest'] ?? null,
                        'blocked'       => $response['result']['blocked'] ?? null,
                        'siteId'        => $siteId ?? null, // assuming this is from your loop
                        'batch_number'        => $batchNumber,
                    ]);

                } catch (\Exception $e) {
                    $syncedClients[] = [
                        'site_id' => $siteId,
                        'mac' => $macAddress,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        return $syncedClients;
    }

    if (latestAccessTokenCheckerError() === 0) {
        print_r(syncClientDetails($batchNumber));
    } else {
        createNewAPITokenAlgo();
        print_r(syncClientDetails($batchNumber));
    }

    return $this->info('Client details have been synced.');
});

Artisan::command('sync-overview-diagram {batchNumber}', function ($batchNumber) {

    function syncOverviewDiagram($batchNumber)
    {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $sites = Sites::all();

        Overviewdiagrams::truncate();

        foreach ($sites as $key => $site) {
            try {
                $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$site['siteId']}/dashboard/overview-diagram", [
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Bearer AccessToken=' . latestAccessToken(),
                    ]
                ]);

                $response = json_decode($res->getBody(), true);

                Overviewdiagrams::create([
                    "totalGatewayNum" => $response['result']['totalGatewayNum'] ?? "N/A",
                    "connectedGatewayNum" => $response['result']['connectedGatewayNum'] ?? "N/A",
                    "disconnectedGatewayNum" => $response['result']['disconnectedGatewayNum'] ?? "N/A",
                    "totalSwitchNum" => $response['result']['totalSwitchNum'] ?? "N/A",
                    "connectedSwitchNum" => $response['result']['connectedSwitchNum'] ?? "N/A",
                    "disconnectedSwitchNum" => $response['result']['disconnectedSwitchNum'] ?? "N/A",
                    "totalPorts" => $response['result']['totalPorts'] ?? "N/A",
                    "availablePorts" => $response['result']['availablePorts'] ?? "N/A",
                    "powerConsumption" => $response['result']['powerConsumption'] ?? "N/A",
                    "totalApNum" => $response['result']['totalApNum'] ?? "N/A",
                    "connectedApNum" => $response['result']['connectedApNum'] ?? "N/A",
                    "isolatedApNum" => $response['result']['isolatedApNum'] ?? "N/A",
                    "disconnectedApNum" => $response['result']['disconnectedApNum'] ?? "N/A",
                    "totalClientNum" => $response['result']['totalClientNum'] ?? "N/A",
                    "wiredClientNum" => $response['result']['wiredClientNum'] ?? "N/A",
                    "wirelessClientNum" => $response['result']['wirelessClientNum'] ?? "N/A",
                    "guestNum" => $response['result']['guestNum'] ?? "N/A",
                    "siteId" => $site['siteId'],
                    'batch_number'        => $batchNumber,
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'errorCode' => 1,
                    'message' => 'Failed to fetch overview diagram.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return "done";
    }

    if (latestAccessTokenCheckerError() === 0) {
         print_r(syncOverviewDiagram($batchNumber));
    } else {
        // if expired
        createNewAPITokenAlgo();
        print_r(syncOverviewDiagram($batchNumber));
    }
    return $this->info('Devices has been sync;');
});

Artisan::command('sync-log-notifications {batchNumber}', function ($batchNumber) {

    function syncLogNotifications($batchNumber)
    {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $sites = Sites::all();


        Lognotifications::truncate();

        foreach ($sites as $key => $site) {
            try {
                $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$site['siteId']}/site/log-notification", [
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Bearer AccessToken=' . latestAccessToken(),
                    ]
                ]);

                $response = json_decode($res->getBody(), true);

                foreach ($response['result']['logNotifications'] as $key => $log) {
                    Lognotifications::create([
                        'key' => $log['key'],
                        'shortMsg' => $log['shortMsg'],
                        'siteId' => $site['siteId'],
                        'batch_number'        => $batchNumber,
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'errorCode' => 1,
                    'message' => 'Failed to fetch log notification data.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return "done";
    }

    if (latestAccessTokenCheckerError() === 0) {
         print_r(syncLogNotifications($batchNumber));
    } else {
        // if expired
        createNewAPITokenAlgo();
        print_r(syncLogNotifications($batchNumber));
    }
    return $this->info('Log notification has been sync;');
});

Artisan::command('detect-offline-sites', function () {

    $currentTime = now();

    // Allow execution only between 5:00 AM and 6:00 PM
    if ($currentTime->between(Carbon::createFromTime(5, 0), Carbon::createFromTime(18, 0))) {

        if (latestAccessTokenCheckerError() === 0) {

            dd(incidentMailerSender());

            $this->info('no new api token');

        } else {

            createNewAPITokenAlgo();

            dd(incidentMailerSender());

            $this->info('has new api token');
        }

    } else {
        $this->info('Command not allowed outside 5:00 AM to 6:00 PM.');
    }

})->purpose('Offline Detection.')->everyMinute();


Artisan::command('sync-batch', function () {
    $latestBatch = Batches::latest('id')->first();

    if ($latestBatch) {
        $latestNumber = (int) str_replace('BATCH_', '', $latestBatch->batch_number);
        $nextNumber = $latestNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $formattedBatch = 'BATCH_' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);

    Batches::create([
        'batch_number' => $formattedBatch,
    ]);

    $commands = [
        'sync-sites',
        'sync-audit-logs',
        'sync-devices',
        'sync-clients',
        'sync-overview-diagram',
        'sync-client-details',
        'sync-log-notifications'
    ];

    foreach ($commands as $cmd) {
        $this->info("Running $cmd...");

        Artisan::call($cmd." ".$formattedBatch);

        // Display the output from the called command
        $this->line(Artisan::output());
    }

    return $this->info("New batch created: $formattedBatch");
})->everyFiveMinutes();;


// Artisan::command('execute', function () {

// });
