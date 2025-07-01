<?php

use App\Jobs\IncidentMailSender;
use App\Mail\IncidentMailer;
use App\Mail\TicketMail;
use App\Models\Auditlogs;
use App\Models\Customers;
use App\Models\Logs;
use App\Models\Sites;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use Smark\Smark\Dater;
use Smark\Smark\JSON;

Artisan::command('sync-from-http', function () {


    // GENERATE NEW API KEY  ========================================================================================================

    function generateNewAPIAccessTokenFromHTTP()
    {
        $postRequestForNewApiKey = Http::withHeaders([
            'Content-Type' => 'application/json',  // Optional, can be inferred from the `json` method
        ])->withOptions([
            'verify' => false,
        ])->post(env('OMADAC_SERVER').'/openapi/authorize/token?grant_type=client_credentials', [
            'omadacId' => env('OMADAC_ID'),
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
        ]);

        // Decode the response body from JSON to an array
        $responseBody = json_decode($postRequestForNewApiKey->body(), true);  // Decode into an associative array

        Logs::create(['log' => 'A new Access Token has been successfully generated on '.Dater::humanReadableDateWithDayAndTime(date('F j, Y g:i:s'))]);

        return JSON::jsonUnshift('accessTokenStorage/accessTokens.json', $responseBody['result']);
    }

    function querySitesDataFromTheDatabaseFromHTTP($latestAccessTokenParam)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites?page=1&pageSize=1000');

        Sites::whereNot('id', '')->delete();

        foreach ($response['result']['data'] as $key => $value) {
            Sites::create([
                'name' => isset($value['name']) ? $value['name'] : null,
                'siteId' => isset($value['siteId']) ? $value['siteId'] : null,
                'region' => isset($value['region']) ? $value['region'] : null,
                'timezone' => isset($value['timeZone']) ? $value['timeZone'] : null,
                'scenario' => isset($value['scenario']) ? $value['scenario'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
                'supportES' => isset($value['supportES']) ? $value['supportES'] : null,
                'supportL2' => isset($value['supportL2']) ? $value['supportL2'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
            ]);
        }

        // Return a success response
        return response()->json([
            'message' => 'Sites data updated successfully!',
        ]);
    }

    // END SITES

    // AUDIT LOGS ======================================================================================================================

    function queryAuditLogsDataFromTheDatabaseFromHTTP($latestAccessTokenParam)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/audit-logs?page=1&pageSize=1000');

        Auditlogs::whereNot('id', '')->delete();

        foreach ($response['result']['data'] as $key => $value) {
            Auditlogs::create([
                'time' => isset($value['time']) ? $value['time'] : null,
                'operator' => isset($value['operator']) ? $value['operator'] : null,
                'resource' => isset($value['resource']) ? $value['resource'] : null,
                'ip' => isset($value['ip']) ? $value['ip'] : null,
                'auditType' => isset($value['auditType']) ? $value['auditType'] : null,
                'level' => isset($value['level']) ? $value['level'] : null,
                'result' => isset($value['result']) ? $value['result'] : null,
                'content' => isset($value['content']) ? $value['content'] : null,
                'content' => isset($value['content']) ? $value['content'] : null,
                'label' => isset($value['label']) ? $value['label'] : null,
                'oldValue' => isset($value['oldValue']) ? $value['oldValue'] : null,
                'newValue' => isset($value['newValue']) ? $value['newValue'] : null,
            ]);
        }

        // Return a success response
        return response()->json([
            'message' => 'Audit Type data updated successfully!',
        ]);
    }

    // END AUDIT LOGS

    // get the stored latest access token

    $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

    // CHECKING API ACCESS TOKEN IF EXPIRED ===========================================================================================

    $response = Http::withHeaders([
        'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
    ])->withOptions([
        'verify' => false,
    ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites?page=1&pageSize=1000');

    $expirationBasisThroughErrorCode = $response['errorCode'];

    // EXECUTE ========================================================================================================================

    if ($expirationBasisThroughErrorCode === 0) {

        // queryCustomersDataFromTheDatabase($latestAccessToken);
        querySitesDataFromTheDatabaseFromHTTP($latestAccessToken);
        queryAuditLogsDataFromTheDatabaseFromHTTP($latestAccessToken);

        Logs::create(['log' => 'The database has been successfully synchronized on '.Dater::humanReadableDateWithDayAndTime(date('F j, Y g:i:s'))]);

    } else {

        // if expired

        generateNewAPIAccessTokenFromHTTP(); // generate new access token

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        // queryCustomersDataFromTheDatabase($latestAccessToken);
        querySitesDataFromTheDatabaseFromHTTP($latestAccessToken);
        queryAuditLogsDataFromTheDatabaseFromHTTP($latestAccessToken);

        Logs::create(['log' => 'The database has been successfully synchronized on '.Dater::humanReadableDateWithDayAndTime(date('F j, Y g:i:s')).' with new generated Access Token.']);
    }
});

Artisan::command('sync', function () {

    // GENERATE NEW API KEY  ========================================================================================================

    function generateNewAPIAccessToken()
    {
        $postRequestForNewApiKey = Http::withHeaders([
            'Content-Type' => 'application/json',  // Optional, can be inferred from the `json` method
        ])->withOptions([
            'verify' => false,
        ])->post(env('OMADAC_SERVER').'/openapi/authorize/token?grant_type=client_credentials', [
            'omadacId' => env('OMADAC_ID'),
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
        ]);

        // Decode the response body from JSON to an array
        $responseBody = json_decode($postRequestForNewApiKey->body(), true);  // Decode into an associative array

        Logs::create(['log' => 'A new Access Token has been successfully generated on '.Dater::humanReadableDateWithDayAndTime(date('F j, Y g:i:s'))]);

        return JSON::jsonUnshift('public/accessTokenStorage/accessTokens.json', $responseBody['result']);
    }

    // CUSTOMERS ======================================================================================================================

    // function queryCustomersDataFromTheDatabase($latestAccessTokenParam)
    // {
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
    //     ])->withOptions([
    //         'verify' => false,
    //     ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/customers?page=1&pageSize=1000');

    //     Customers::whereNot('id', '')->delete();

    //     foreach ($response['result']['data'] as $key => $value) {
    //         Customers::create([
    //             'customerId' => isset($value['customerId']) ? $value['customerId'] : null,
    //             'name' => isset($value['customerName']) ? $value['customerName'] : null,
    //             'description' => isset($value['description']) ? $value['description'] : null, // Check if description exists
    //             'users_id' => 1, // changed
    //         ]);
    //     }
    // }

    // END CUSTOMERS

    // SITES ======================================================================================================================

    // function querySitesDataFromTheDatabase($latestAccessTokenParam)
    // {
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
    //     ])->withOptions([
    //         'verify' => false,
    //     ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites?page=1&pageSize=1000');

    //     Sites::whereNot('id', '')->delete();

    //     foreach ($response['result']['data'] as $key => $value) {
    //         Sites::create([
    //             'name' => isset($value['siteName']) ? $value['siteName'] : null,
    //             'siteId' => isset($value['siteId']) ? $value['siteId'] : null,
    //             'customerId' => isset($value['customerId']) ? $value['customerId'] : null,
    //             'customerName' => isset($value['customerName']) ? $value['customerName'] : null,
    //             'region' => isset($value['region']) ? $value['region'] : null,
    //             'timezone' => isset($value['timeZone']) ? $value['timeZone'] : null,
    //             'scenario' => isset($value['scenario']) ? $value['scenario'] : null,
    //             'wan' => isset($value['wan']) ? $value['wan'] : null,
    //             'connectedApNum' => isset($value['connectedApNum']) ? $value['connectedApNum'] : null,
    //             'disconnectedApNum' => isset($value['disconnectedApNum']) ? $value['disconnectedApNum'] : null,
    //             'isolatedApNum' => isset($value['isolatedApNum']) ? $value['isolatedApNum'] : null,
    //             'connectedSwitchNum' => isset($value['connectedSwitchNum']) ? $value['connectedSwitchNum'] : null,
    //             'disconnectedSwitchNum' => isset($value['disconnectedSwitchNum']) ? $value['disconnectedSwitchNum'] : null,
    //             'type' => isset($value['type']) ? $value['type'] : null,
    //         ]);
    //     }

    //     // Return a success response
    //     return response()->json([
    //         'message' => 'Sites data updated successfully!',
    //     ]);
    // }

    function querySitesDataFromTheDatabase($latestAccessTokenParam)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites?page=1&pageSize=1000');

        Sites::whereNot('id', '')->delete();

        foreach ($response['result']['data'] as $key => $value) {
            Sites::create([
                'name' => isset($value['name']) ? $value['name'] : null,
                'siteId' => isset($value['siteId']) ? $value['siteId'] : null,
                'region' => isset($value['region']) ? $value['region'] : null,
                'timezone' => isset($value['timeZone']) ? $value['timeZone'] : null,
                'scenario' => isset($value['scenario']) ? $value['scenario'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
                'supportES' => isset($value['supportES']) ? $value['supportES'] : null,
                'supportL2' => isset($value['supportL2']) ? $value['supportL2'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
            ]);
        }

        // Return a success response
        return response()->json([
            'message' => 'Sites data updated successfully!',
        ]);
    }

    // END SITES

    // AUDIT LOGS ======================================================================================================================

    function queryAuditLogsDataFromTheDatabase($latestAccessTokenParam)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessTokenParam, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/audit-logs?page=1&pageSize=1000');

        Auditlogs::whereNot('id', '')->delete();

        foreach ($response['result']['data'] as $key => $value) {
            Auditlogs::create([
                'time' => isset($value['time']) ? $value['time'] : null,
                'operator' => isset($value['operator']) ? $value['operator'] : null,
                'resource' => isset($value['resource']) ? $value['resource'] : null,
                'ip' => isset($value['ip']) ? $value['ip'] : null,
                'auditType' => isset($value['auditType']) ? $value['auditType'] : null,
                'level' => isset($value['level']) ? $value['level'] : null,
                'result' => isset($value['result']) ? $value['result'] : null,
                'content' => isset($value['content']) ? $value['content'] : null,
                'content' => isset($value['content']) ? $value['content'] : null,
                'label' => isset($value['label']) ? $value['label'] : null,
                'oldValue' => isset($value['oldValue']) ? $value['oldValue'] : null,
                'newValue' => isset($value['newValue']) ? $value['newValue'] : null,
            ]);
        }

        // Return a success response
        return response()->json([
            'message' => 'Audit Type data updated successfully!',
        ]);
    }

    // END AUDIT LOGS

    // get the stored latest access token

    $latestAccessToken = JSON::jsonRead('public/accessTokenStorage/accessTokens.json')[0]['accessToken'];

    // CHECKING API ACCESS TOKEN IF EXPIRED ===========================================================================================

    $response = Http::withHeaders([
        'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
    ])->withOptions([
        'verify' => false,
    ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites?page=1&pageSize=1000');

    $expirationBasisThroughErrorCode = $response['errorCode'];

    // EXECUTE ========================================================================================================================

    if ($expirationBasisThroughErrorCode === 0) {

        // queryCustomersDataFromTheDatabase($latestAccessToken);
        querySitesDataFromTheDatabase($latestAccessToken);
        queryAuditLogsDataFromTheDatabase($latestAccessToken);

        Logs::create(['log' => 'The database has been successfully synchronized on '.Dater::humanReadableDateWithDayAndTime(date('F j, Y g:i:s'))]);

    } else {

        // if expired

        generateNewAPIAccessToken(); // generate new access token

        $latestAccessToken = JSON::jsonRead('public/accessTokenStorage/accessTokens.json')[0]['accessToken'];

        // queryCustomersDataFromTheDatabase($latestAccessToken);
        querySitesDataFromTheDatabase($latestAccessToken);
        queryAuditLogsDataFromTheDatabase($latestAccessToken);

        Logs::create(['log' => 'The database has been successfully synchronized on '.Dater::humanReadableDateWithDayAndTime(date('F j, Y g:i:s')).' with new generated Access Token.']);
    }

    $this->info('PHP ARTISAN SYNC HAS BEEN EXECUTED.');

})->purpose('Sync data from the API.');






// latest access token

function latestAccessToken()
{
    return JSON::jsonRead('public/accessTokenStorage/accessTokens.json')[0]['accessToken'];
}

function createNewAPITokenAlgo()
{
    $postRequestForNewApiKey = Http::withHeaders([
        'Content-Type' => 'application/json',  // Optional, can be inferred from the `json` method
    ])->withOptions([
        'verify' => false,
    ])->post(env('OMADAC_SERVER').'/openapi/authorize/token?grant_type=client_credentials', [
        'omadacId' => env('OMADAC_ID'),
        'client_id' => env('CLIENT_ID'),
        'client_secret' => env('CLIENT_SECRET'),
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
        'verify' => false,
    ])->get(
        env('OMADAC_SERVER') .
        '/openapi/v1/' . env('OMADAC_ID') .
        '/sites/' . $siteId .
        '/dashboard/traffic-activities?start=' . $start . '&end=' . $end
    );

    return $response;
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
                    'site' => $site['site'],
                    'device' => [
                        'name' => $device['name'],
                        'mac' => $device['mac'],
                        'type' => $device['type'],
                    ],
                    'status' => 'offline'
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
            'name' => $site['name'],
            'siteId' => $site['siteId'],
            'main_data' => $switchTrafficActivities,
        ];
    }

    $FINAL_LIST_OF_OFFLINE_SITES = [];

    foreach ($trafficData as $site) {
        $latestEntry = null;

        foreach ($site['main_data'] as $entry) {
            if (
                isset($entry['txData'], $entry['dxData'], $entry['time']) &&
                $entry['txData'] != 0.0 &&
                $entry['dxData'] != 0.0
            ) {
                // Compare times
                if (!$latestEntry || $entry['time'] > $latestEntry['time']) {
                    $latestEntry = $entry;
                }
            }
        }

        if ($latestEntry) {
            $FINAL_LIST_OF_OFFLINE_SITES[] = [
                'name' => $site['name'],
                'siteId' => $site['siteId'],
                'time' => (new DateTime("@{$latestEntry['time']}"))
                    ->setTimezone(new DateTimeZone('Asia/Manila'))
                    ->format('F j, Y \a\t g:i A'),
            ];
        }
    }

    // $FINAL_LIST_OF_OFFLINE_SITES
    // $FINAL_OFFLINE_DEVICES

    IncidentMailSender::dispatch($FINAL_LIST_OF_OFFLINE_SITES);

    return "Offline Notification Sent!";
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

Artisan::command('sample', function () {

    if (latestAccessTokenCheckerError() === 0) {

        dd(incidentMailerSender());

        $this->info('no new api token');

    } else {

        // if expired
        createNewAPITokenAlgo();

        dd(incidentMailerSender());

        $this->info('has new api token');
    }
})->purpose('Sync data.')->everyMinute();
