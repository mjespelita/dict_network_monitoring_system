<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// end of import

use App\Http\Controllers\LogsController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Models\Logs;
use Smark\Smark\Cache;

// end of import

use App\Http\Controllers\CustomersController;
use App\Models\Customers;

// end of import

use App\Http\Controllers\UploadsController;
use App\Models\Uploads;

// end of import

use App\Http\Controllers\SitesController;
use App\Models\Sites;
use Illuminate\Support\Facades\Http;

// end of import

use App\Http\Controllers\AuditlogsController;
use App\Models\Auditlogs;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Smark\Smark\Dater;
use Smark\Smark\JSON;
use Smark\Smark\PDFer;
use App\Models\User;

// end of import


Route::get('/', function () {
    return view('welcome');
});

Route::get('/users', function () {
    return response()->json(User::all());
});

Route::post('/add-user', function (Request $request) {
    return response()->json($request);
});

Route::post('/sample-post-api', function (Request $request) {
    $request->validate([
        'sample' => 'required'
    ]);

    echo $request->sample;
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware(AuthMiddleware::class);

    Route::get('/admin-dashboard', function () {
        return view('admin-dashboard');
    })->middleware(AdminMiddleware::class);

    Route::get('/proxy', function () {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken=AT-77In5x0OvaHrA9I53OG90zcecX9iElHI', // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get('https://10.99.0.187:8043/openapi/v1/msp/950c1327d64a1b53de3882530e979b99/customers?page=1&pageSize=1000');

        return $response->json();
    });

    Route::get('/database-sync', function () {
        $response = Logs::whereDate('created_at', Carbon::today())->get();
        return $response;
    });

    // Access Token

    // FROM OMADA API ===============================================================================================================================

    Route::get('/traffic-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites/'.$siteId.'/dashboard/traffic-activities?start='.$start.'&end='.$end);

        return json_decode($response->body(), true);
    });

    Route::get('/top-cpu-usage-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {
        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
        ])->withOptions([
            'verify' => false, // Disable SSL verification if needed
        ])->get(env('OMADAC_SERVER') . '/openapi/v1/' . env('OMADAC_ID') . '/sites/' . $siteId . '/dashboard/top-device-cpu-usage', [
            'start' => $start,
            'end' => $end,
        ]);

        return json_decode($response->body(), true);
    });

    Route::get('/top-memory-usage-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {
        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER') . '/openapi/v1/' . env('OMADAC_ID') . '/sites/' . $siteId . '/dashboard/top-device-memory-usage?start=' . $start . '&end=' . $end);

        return json_decode($response->body(), true);
    });

    Route::get('/devices-api/{siteId}', function ($siteId) {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/devices?page=1&pageSize=1000", [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer AccessToken=' . $latestAccessToken, // Use appropriate token source
                ]
            ]);

            return response()->json(json_decode($res->getBody(), true));
        } catch (\Exception $e) {
            return response()->json([
                'errorCode' => 1,
                'message' => 'Failed to fetch device data.',
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::get('/clients-api/{siteId}', function ($siteId) {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $accessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/clients?page=1&pageSize=1000", [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer AccessToken=' . $accessToken,
                ]
            ]);

            return response()->json(json_decode($res->getBody(), true));
        } catch (\Exception $e) {
            return response()->json([
                'errorCode' => 1,
                'message' => 'Failed to fetch client data.',
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::get('/client-details-api/{siteId}/{macAddress}', function ($siteId, $macAddress) {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $accessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/clients/{$macAddress}", [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer AccessToken=' . $accessToken,
                ]
            ]);

            return response()->json(json_decode($res->getBody(), true));
        } catch (\Exception $e) {
            return response()->json([
                'errorCode' => 1,
                'message' => 'Failed to fetch client detail.',
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::get('/log-notification-api/{siteId}', function ($siteId) {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $accessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/site/log-notification", [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer AccessToken=' . $accessToken,
                ]
            ]);

            return response()->json(json_decode($res->getBody(), true));
        } catch (\Exception $e) {
            Log::error("Log Notification API Error: " . $e->getMessage());
            return response()->json([
                'errorCode' => 1,
                'message' => 'Failed to fetch log notification data.',
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::get('/overview-diagram-api/{siteId}', function ($siteId) {
        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');
        $accessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/dashboard/overview-diagram", [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer AccessToken=' . $accessToken,
                ]
            ]);

            return response()->json(json_decode($res->getBody(), true));
        } catch (\Exception $e) {
            return response()->json([
                'errorCode' => 1,
                'message' => 'Failed to fetch overview diagram.',
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::get('/generate-new-api-token', function () {
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
    });

    Route::get('/traffic-api-access-token', function () {
        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];
        return response()->json($latestAccessToken);
    });

    // chopped data ===========================

    // bandwidth usage

    Route::get('/get-bandwidth-usage-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {

        function formatBandwidthSpeedGetBandwidthUsageApi($bps)
        {
            $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
            $i = 0;
            while ($bps >= 1000 && $i < count($units) - 1) {
                $bps /= 1000;
                $i++;
            }
            return number_format($bps, 2) . ' ' . $units[$i];
        }

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        // TRAFFIC ACTIVITIES

        $responseTrafficActivities = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites/'.$siteId.'/dashboard/traffic-activities?start='.$start.'&end='.$end);

        $decodedResponseTrafficActivities = json_decode($responseTrafficActivities->body(), true);

        // average bandwidth speed -------------------------------------------------------------------------------------------------------

        $totalTx = 0;
        $totalDx = 0;

        $trafficItems = $decodedResponseTrafficActivities['result']['switchTrafficActivities'] ?? [];

        if (!empty($trafficItems)) {
            foreach ($trafficItems as $item) {
                if (isset($item['txData'])) {
                    $totalTx += (float) $item['txData']; // in MB
                }
                if (isset($item['dxData'])) {
                    $totalDx += (float) $item['dxData'];
                }
            }

            // Filter only items with actual traffic data
            $validEntries = array_filter(
                $trafficItems,
                fn($item) => isset($item['txData']) || isset($item['dxData'])
            );

            if (!empty($validEntries)) {
                $first = reset($validEntries);
                $last = end($validEntries);
                $startTimestamp = $first['time'];
                $endTimestamp = $last['time'];
            } else {
                $startTimestamp = $endTimestamp = time(); // fallback
            }

            // Format total data usage
            // $uploadFormatted = formatDataSizeOnAvgBandwidth($totalTx);
            // $downloadFormatted = formatDataSizeOnAvgBandwidth($totalDx);

            // Compute average speed
            $durationSeconds = max(1, $endTimestamp - $startTimestamp);

            $totalTxBits = $totalTx * 1024 * 1024 * 8;
            $totalDxBits = $totalDx * 1024 * 1024 * 8;

            $avgUploadBps = $totalTxBits / $durationSeconds;
            $avgDownloadBps = $totalDxBits / $durationSeconds;

            $uploadAvgSpeed = formatBandwidthSpeedGetBandwidthUsageApi($avgUploadBps);
            $downloadAvgSpeed = formatBandwidthSpeedGetBandwidthUsageApi($avgDownloadBps);
        }

        return response()->json([
            'uploadAvgSpeed' => $uploadAvgSpeed,
            'downloadAvgSpeed' => $downloadAvgSpeed,
        ]);

        // end average bandwidth speed -------------------------------------------------------------------------------------------------------

    });

    // total download and upload

    Route::get('/get-total-upload-download-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {

        function formatDataSizeUploadDownloadApi($mbValue) {
            $bytes = $mbValue * 1024 * 1024; // Convert MB to Bytes
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $unitIndex = 0;

            while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
                $bytes /= 1024;
                $unitIndex++;
            }

            return number_format($bytes, 2) . ' ' . $units[$unitIndex];
        }

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        // TRAFFIC ACTIVITIES

        $responseTrafficActivities = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites/'.$siteId.'/dashboard/traffic-activities?start='.$start.'&end='.$end);

        $decodedResponseTrafficActivities = json_decode($responseTrafficActivities->body(), true);

        $trafficData = $decodedResponseTrafficActivities['result']['switchTrafficActivities'] ?? [];

        // total download and upload ------------------------------------------------------------------------------------------------

        $totalTx = 0;
        $totalDx = 0;

        foreach ($trafficData as $item) {
            if (isset($item['txData']) && isset($item['dxData'])) {
                $totalTx += (float)$item['txData'];
                $totalDx += (float)$item['dxData'];
            }
        }

        $uploadFormatted = formatDataSizeUploadDownloadApi($totalTx);
        $downloadFormatted = formatDataSizeUploadDownloadApi($totalDx);

        return response()->json([
            'uploadFormatted' => $uploadFormatted,
            'downloadFormatted' => $downloadFormatted,
        ]);

        // end of total download and upload ------------------------------------------------------------------------------------------------

    });

    // offline days

    Route::get('/get-percentage-availability-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        // TRAFFIC ACTIVITIES

        $responseTrafficActivities = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites/'.$siteId.'/dashboard/traffic-activities?start='.$start.'&end='.$end);

        $decodedResponseTrafficActivities = json_decode($responseTrafficActivities->body(), true);

        $trafficData = $decodedResponseTrafficActivities['result']['switchTrafficActivities'] ?? [];

        // percentage availability ----------------------------------------------------------------------------------------------

        // // Step 1: Parse timestamps into unique dates with traffic
        $trafficData = $decodedResponseTrafficActivities['result']['switchTrafficActivities'];
        $dateMap = [];

        foreach ($trafficData as $item) {
            if (!empty($item['txData']) || !empty($item['dxData'])) {
                $dateKey = date('Y-m-d', $item['time']); // e.g., "2025-05-08"
                $dateMap[$dateKey] = true;
            }
        }

        $missingDates = [];
        $currentMissingRange = [];

        foreach ($trafficData as $item) {
            $date = (new DateTime('@' . $item['time']))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d');

            if (empty($item['txData']) && empty($item['dxData'])) {
                $currentMissingRange[] = $date;
            } else {
                // Close current missing range if exists
                if (!empty($currentMissingRange)) {
                    $missingDates[] = $currentMissingRange;
                    $currentMissingRange = [];
                }
            }
        }

        // Handle the final range if the array ends with missing data
        if (!empty($currentMissingRange)) {
            $missingDates[] = $currentMissingRange;
        }

        // Format the output into readable ranges
        $formattedRanges = [];

        foreach ($missingDates as $range) {
            $startOfflines = date('M d Y', strtotime($range[0]));
            $endOfflines = date('M d Y', strtotime(end($range)));

            if ($startOfflines === $endOfflines) {
                $formattedRanges[] = $startOfflines;
            } else {
                $formattedRanges[] = "$startOfflines - $endOfflines";
            }
        }

        $startDate = Carbon::createFromTimestamp($start);
        $endDate = Carbon::createFromTimestamp($end);
        $allDates = CarbonPeriod::create($startDate, $endDate);

        $allDateKeys = [];
        foreach ($allDates as $date) {
            $allDateKeys[] = $date->format('Y-m-d');
        }

        // Step 3: Count offline days
        $offlineDays = 0;
        foreach ($allDateKeys as $date) {
            if (!isset($dateMap[$date])) {
                $offlineDays++;
            }
        }

        // Step 4: Calculate availability percent
        $daysInFirstMonth = $startDate->copy()->endOfMonth()->day;
        $totalDaysWithData = $daysInFirstMonth - $offlineDays;
        $availabilityPercent = round(($totalDaysWithData / $daysInFirstMonth) * 100);

        return response()->json($availabilityPercent);

        // end of percentage availability -------------------------------------------------------------------------------------------

    });

    Route::get('/export-general-data-into-pdf', function (Request $request) {
        $request->validate([
            'site' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'project' => 'required',
            'supplier' => 'required',
            'acceptanceDate' => 'required|date',
            'people' => 'required|array|min:1',
            'people.*.purpose' => 'string',
            'people.*.name' => 'string',
            'people.*.designation' => 'string',
        ]);

        $dataHolder = [];

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];
        $startTimestamp = Carbon::parse($request->startDate, 'UTC')->timestamp;
        $endTimestamp = Carbon::parse($request->endDate, 'UTC')->timestamp;

        /**
         * first loop the sites and get their SITES_ID
         */

        $sites = Sites::all();

        // reusable functions ---------------------------------------------------------------------------------------------------

        function formatDataSize($mbValue) {
            $bytes = $mbValue * 1024 * 1024; // Convert MB to Bytes
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $unitIndex = 0;

            while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
                $bytes /= 1024;
                $unitIndex++;
            }

            return number_format($bytes, 2) . ' ' . $units[$unitIndex];
        }

        function formatDataSizeOnAvgBandwidth($mb)
        {
            $bytes = $mb * 1024 * 1024;
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return number_format($bytes, 2) . ' ' . $units[$i];
        }

        function formatBandwidthSpeed($bps)
        {
            $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
            $i = 0;
            while ($bps >= 1000 && $i < count($units) - 1) {
                $bps /= 1000;
                $i++;
            }
            return number_format($bps, 2) . ' ' . $units[$i];
        }

        function parseSizeForCombination($sizeStr) {
            $sizeStr = trim($sizeStr);
            if ($sizeStr === '0' || $sizeStr === '0.00 B') return 0;

            preg_match('/([\d.]+)\s*(B|KB|MB|GB|TB)/i', $sizeStr, $matches);
            $value = (float)$matches[1];
            $unit = strtoupper($matches[2]);

            $units = ['B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4];
            return $value * pow(1024, $units[$unit] ?? 0);
        }

        function formatDataSizeInCombination($bytes) {
            if ($bytes == 0) return '0.00 B';
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $exp = (int)floor(log($bytes, 1024));
            return sprintf('%.2f %s', $bytes / pow(1024, $exp), $units[$exp]);
        }

        function mergeCombinedStats(array $data) {
            $merged = [];

            $totalUsers = 0;
            $totalUniqueUsers = 0;
            $totalUploadBytes = 0;
            $totalDownloadBytes = 0;

            foreach ($data as $item) {
                foreach ($item['_combined_stats'] ?? [] as $stat) {
                    $date = $stat['date'];

                    $users = $stat['users'] ?? 0;
                    $uniqueUsers = $stat['uniqueUsers'] ?? 0;
                    $uploadBytes = parseSizeForCombination($stat['upload'] ?? '0 B');
                    $downloadBytes = parseSizeForCombination($stat['download'] ?? '0 B');

                    if (!isset($merged[$date])) {
                        $merged[$date] = [
                            'date' => $date,
                            'users' => 0,
                            'uniqueUsers' => 0,
                            'uploadBytes' => 0,
                            'downloadBytes' => 0,
                        ];
                    }

                    $merged[$date]['users'] += $users;
                    $merged[$date]['uniqueUsers'] += $uniqueUsers;
                    $merged[$date]['uploadBytes'] += $uploadBytes;
                    $merged[$date]['downloadBytes'] += $downloadBytes;

                    // Totals
                    $totalUsers += $users;
                    $totalUniqueUsers += $uniqueUsers;
                    $totalUploadBytes += $uploadBytes;
                    $totalDownloadBytes += $downloadBytes;
                }
            }

            $formattedData = array_map(function ($item) {
                return [
                    'date' => $item['date'],
                    'users' => $item['users'],
                    'uniqueUsers' => $item['uniqueUsers'],
                    'upload' => formatDataSizeInCombination($item['uploadBytes']),
                    'download' => formatDataSizeInCombination($item['downloadBytes']),
                ];
            }, array_values($merged));

            return [
                'data' => $formattedData,
                'totals' => [
                    'users' => $totalUsers,
                    'uniqueUsers' => $totalUniqueUsers,
                    'upload' => formatDataSizeInCombination($totalUploadBytes),
                    'download' => formatDataSizeInCombination($totalDownloadBytes),
                ]
            ];
        }


        // end of reusable functions --------------------------------------------------------------------------------------------

        foreach ($sites as $key => $value) {

            // TOP CPU USAGE

            $responseTopCPUUsage = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
            ])->withOptions([
                'verify' => false, // Disable SSL verification if needed
            ])->get(env('OMADAC_SERVER') . '/openapi/v1/' . env('OMADAC_ID') . '/sites/' . $value['siteId'] . '/dashboard/top-device-cpu-usage', [
                'start' => $startTimestamp,
                'end' => $endTimestamp,
            ]);

            $decodedresponseTopCPUUsage = json_decode($responseTopCPUUsage->body(), true);

            // TOP MEMORY USAGE

            $responseTopMemoryUsage = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
            ])->withOptions([
                'verify' => false,
            ])->get(env('OMADAC_SERVER') . '/openapi/v1/' . env('OMADAC_ID') . '/sites/' . $value['siteId'] . '/dashboard/top-device-memory-usage?start=' . $startTimestamp . '&end=' . $endTimestamp);

            $decodedResponseTopMemoryUsage = json_decode($responseTopMemoryUsage->body(), true);

            // DEVICES

            $responseDevices = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
            ])->withOptions([
                'verify' => false,
            ])->get(env('OMADAC_SERVER') . '/openapi/v1/'. env('OMADAC_ID') . '/sites/' . $value['siteId'] . '/devices?page=1&pageSize=1000');

            $decodedResponseDevices = json_decode($responseDevices->getBody(), true);

            // CLIENTS

            $responseClients = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
            ])->withOptions([
                'verify' => false,
            ])->get(env('OMADAC_SERVER') . '/openapi/v1/'. env('OMADAC_ID') . '/sites/' . $value['siteId'] . '/clients?page=1&pageSize=1000');

            $decodedResponseClients = json_decode($responseClients->getBody(), true);

            // USERS AND UNIQUE USERS

            $responseUsersAndUniqueUsers = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
            ])->withOptions([
                'verify' => false,
            ])->get(env('OMADAC_SERVER') . '/openapi/v1/'. env('OMADAC_ID') . '/sites/' . $value['siteId'] . '/insight/past-connection?page=1&pageSize=1000&filters.timeStart=' . $startTimestamp . '000&filters.timeEnd=' . $endTimestamp . '000');

            $decodedResponseUsersAndUniqueUsers = json_decode($responseUsersAndUniqueUsers->getBody(), true);

            $usersAndUniqueUsers = $decodedResponseUsersAndUniqueUsers['result']['data'];

            // Count total users
            $totalUsers = count($usersAndUniqueUsers);

            // Extract MAC addresses and count unique ones
            $macs = array_column($usersAndUniqueUsers, 'mac');
            $uniqueMacs = array_unique($macs);
            $uniqueUsers = count($uniqueMacs);

            // Result
            $resultUsersAndUniqueUsers = [
                'users' => $totalUsers,
                'uniqueUsers' => $uniqueUsers
            ];

            $usersGroupedByDate = [];

            foreach ($usersAndUniqueUsers as $entry) {
                if (!isset($entry['firstSeen'], $entry['mac'])) continue;

                // Convert firstSeen (milliseconds) to Y-m-d date string
                $date = Carbon::createFromTimestamp($entry['firstSeen'] / 1000)->format('Y-m-d');

                if (!isset($usersGroupedByDate[$date])) {
                    $usersGroupedByDate[$date] = [
                        'count' => 0,
                        'macs' => [],
                    ];
                }

                $usersGroupedByDate[$date]['count']++;
                $usersGroupedByDate[$date]['macs'][] = $entry['mac'];
            }

            // Define your date range
            $startDate = Carbon::parse($request->startDate); // or a fixed string like '2025-05-01'
            $endDate = Carbon::parse($request->endDate);

            // Build the result array with all dates
            $resultUsersAndUniqueUsersPerDay = [];

            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $displayDate = $date->format('M j, Y');

                $users = $usersGroupedByDate[$key]['count'] ?? 0;
                $uniqueUsers = isset($usersGroupedByDate[$key]['macs']) ? count(array_unique($usersGroupedByDate[$key]['macs'])) : 0;

                $resultUsersAndUniqueUsersPerDay[] = [
                    'date' => $displayDate,
                    'users' => $users,
                    'uniqueUsers' => $uniqueUsers,
                ];
            }


            // CLIENT DETAILS

            // LOG NOTIFICATION

            // OVERVIEW DIAGRAM API

            $responseOverviewDiagram = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
            ])->withOptions([
                'verify' => false,
            ])->get(env('OMADAC_SERVER') . '/openapi/v1/'. env('OMADAC_ID') . '/sites/' . $value['siteId'] . '/dashboard/overview-diagram');

            $decodedResponseOverviewDiagram = response()->json(json_decode($responseOverviewDiagram->getBody(), true));

            // TRAFFIC ACTIVITIES

            $responseTrafficActivities = Http::withHeaders([
                'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
            ])->withOptions([
                'verify' => false,
            ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites/'.$value['siteId'].'/dashboard/traffic-activities?start='.Carbon::parse($request->startDate, 'UTC')->timestamp.'&end='.Carbon::parse($request->endDate, 'UTC')->addDays(1)->timestamp);

            $decodedResponseTrafficActivities = json_decode($responseTrafficActivities->body(), true);

            // average bandwidth speed -------------------------------------------------------------------------------------------------------

            $totalTx = 0;
            $totalDx = 0;

            $trafficItems = $decodedResponseTrafficActivities['result']['switchTrafficActivities'] ?? [];

            if (!empty($trafficItems)) {
                foreach ($trafficItems as $item) {
                    if (isset($item['txData'])) {
                        $totalTx += (float) $item['txData']; // in MB
                    }
                    if (isset($item['dxData'])) {
                        $totalDx += (float) $item['dxData'];
                    }
                }

                // Filter only items with actual traffic data
                $validEntries = array_filter(
                    $trafficItems,
                    fn($item) => isset($item['txData']) || isset($item['dxData'])
                );

                if (!empty($validEntries)) {
                    $first = reset($validEntries);
                    $last = end($validEntries);
                    $startTimestamp = $first['time'];
                    $endTimestamp = $last['time'];
                } else {
                    $startTimestamp = $endTimestamp = time(); // fallback
                }

                // Format total data usage
                $uploadFormatted = formatDataSizeOnAvgBandwidth($totalTx);
                $downloadFormatted = formatDataSizeOnAvgBandwidth($totalDx);

                // Compute average speed
                $durationSeconds = max(1, $endTimestamp - $startTimestamp);

                $totalTxBits = $totalTx * 1024 * 1024 * 8;
                $totalDxBits = $totalDx * 1024 * 1024 * 8;

                $avgUploadBps = $totalTxBits / $durationSeconds;
                $avgDownloadBps = $totalDxBits / $durationSeconds;

                $uploadAvgSpeed = formatBandwidthSpeed($avgUploadBps);
                $downloadAvgSpeed = formatBandwidthSpeed($avgDownloadBps);
            }

            // end average bandwidth speed -------------------------------------------------------------------------------------------------------

            // percentage availability ----------------------------------------------------------------------------------------------

            // // Step 1: Parse timestamps into unique dates with traffic
            $trafficData = $decodedResponseTrafficActivities['result']['switchTrafficActivities'];
            $dateMap = []; // date that is online

            foreach ($trafficData as $item) {
                if (!empty($item['txData']) || !empty($item['dxData'])) {
                    $dateKey = date('Y-m-d', $item['time']); // e.g., "2025-05-08"
                    $dateMap[$dateKey] = true;
                }
            }

            $missingDates = [];
            $currentMissingRange = [];

            foreach ($trafficData as $item) {
                $date = (new DateTime('@' . $item['time']))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d');

                if (empty($item['txData']) && empty($item['dxData'])) {
                    $currentMissingRange[] = $date;
                } else {
                    // Close current missing range if exists
                    if (!empty($currentMissingRange)) {
                        $missingDates[] = $currentMissingRange;
                        $currentMissingRange = [];
                    }
                }
            }

            // Handle the final range if the array ends with missing data
            if (!empty($currentMissingRange)) {
                $missingDates[] = $currentMissingRange;
            }

            // Format the output into readable ranges
            $formattedRanges = [];

            foreach ($missingDates as $range) {
                $start = date('M d Y', strtotime($range[0]));
                $end = date('M d Y', strtotime(end($range)));

                if ($start === $end) {
                    $formattedRanges[] = $start;
                } else {
                    $formattedRanges[] = "$start - $end";
                }
            }

            // Step 2: Generate all dates in range
            $startDate = Carbon::createFromTimestamp(Carbon::parse($request->startDate, 'UTC')->timestamp)->startOfDay();
            $endDate = Carbon::createFromTimestamp(Carbon::parse($request->endDate, 'UTC')->timestamp)->endOfDay();
            $allDates = CarbonPeriod::create($startDate, $endDate);

            $allDateKeys = [];
            foreach ($allDates as $date) {
                $allDateKeys[] = $date->format('Y-m-d');
            }

            // Step 3: Count offline days
            $offlineDays = 0;
            foreach ($allDateKeys as $date) {
                if (!isset($dateMap[$date])) {
                    $offlineDays++;
                }
            }

            // Step 4: Calculate availability percent
            $daysInFirstMonth = $startDate->copy()->endOfMonth()->day;
            $totalDaysWithData = $daysInFirstMonth - $offlineDays;
            $availabilityPercent = round(($totalDaysWithData / $daysInFirstMonth) * 100);

            // end of percentage availability -------------------------------------------------------------------------------------------

            // total download and upload ------------------------------------------------------------------------------------------------

            $totalTx = 0;
            $totalDx = 0;

            foreach ($trafficData as $item) {
                if (isset($item['txData']) && isset($item['dxData'])) {
                    $totalTx += (float)$item['txData'];
                    $totalDx += (float)$item['dxData'];
                }
            }

            $uploadFormatted = formatDataSize($totalTx);
            $downloadFormatted = formatDataSize($totalDx);

            // end of total download and upload ------------------------------------------------------------------------------------------------

            // must excel ---------------------------------------------------------------------------------------------------------------------

            // Step 1: Group TRAFFIC DATA by date
            $trafficByDate = [];

            foreach ($trafficData as $item) {
                $timestamp = $item['time'] ?? null;

                if (!$timestamp) continue;

                $dateKey = Carbon::createFromTimestamp($timestamp)->format('Y-m-d');

                if (!isset($trafficByDate[$dateKey])) {
                    $trafficByDate[$dateKey] = ['tx' => 0, 'dx' => 0];
                }

                $trafficByDate[$dateKey]['tx'] += (float)($item['txData'] ?? 0);
                $trafficByDate[$dateKey]['dx'] += (float)($item['dxData'] ?? 0);
            }

            // Step 2: Group USER DATA by date

            // Step 3: Build dailyStats array
            $dailyStats = [];
            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $displayDate = $date->format('M j, Y');

                $upload = isset($trafficByDate[$dateKey]) ? formatDataSize($trafficByDate[$dateKey]['tx']) : '0';
                $download = isset($trafficByDate[$dateKey]) ? formatDataSize($trafficByDate[$dateKey]['dx']) : '0';

                $dailyStats[] = [
                    'date' => $displayDate,
                    'upload' => $upload,
                    'download' => $download
                ];
            }

            // end of must excel  --------------------------------------------------------------------------------------------------------------

            // combined stats -----------------------------------------------------------------------------------------------------------------------

            // Build the combined stats
            $combinedDailyStats = [];
            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $displayDate = $date->format('M j, Y');

                $users = $usersGroupedByDate[$dateKey]['count'] ?? 0;
                $uniqueUsers = isset($usersGroupedByDate[$dateKey]['macs']) ? count(array_unique($usersGroupedByDate[$dateKey]['macs'])) : 0;

                $upload = isset($trafficByDate[$dateKey]) ? formatDataSize($trafficByDate[$dateKey]['tx']) : '0';
                $download = isset($trafficByDate[$dateKey]) ? formatDataSize($trafficByDate[$dateKey]['dx']) : '0';

                $combinedDailyStats[] = [
                    'date' => $displayDate,
                    'users' => $users,
                    'uniqueUsers' => $uniqueUsers,
                    'upload' => $upload,
                    'download' => $download,
                ];
            }

            // end combined stats ----------------------------------------------------------------------------------------------------------------------

            // random

            // end random

            array_push($dataHolder, [
                // '_result_users_and_unique_users_per_day' => $resultUsersAndUniqueUsersPerDay,
                // '_total_upload_download' => $dailyStats,
                // '_FINALLLL' => mergeCombinedStats($dataHolder),
                '_combined_stats' => $combinedDailyStats,
                '_usersAndUniqueUsers' => $resultUsersAndUniqueUsers,
                '_unix' => [
                    '_start_date' => Carbon::parse($request->startDate, 'UTC')->timestamp,
                    '_end_date' => Carbon::parse($request->endDate, 'UTC')->timestamp,
                ],
                '_offline_days' => $formattedRanges,
                '_dates' => [
                    '_start_date' => Dater::humanReadableDateWithDay($request->startDate),
                    '_end_date' => Dater::humanReadableDateWithDay($request->endDate),
                ],
                '_name' => $value['name'],
                '_siteId' => $value['siteId'],
                '_percent_uptime' => $availabilityPercent . '%', // percentage availability here
                '_uploadFormatted' => $uploadFormatted,
                '_downloadFormatted' => $downloadFormatted,
                '_uploadAvgSpeed' => $uploadAvgSpeed,
                '_downloadAvgSpeed' => $downloadAvgSpeed,
                '_top_cpu_usage' => $decodedresponseTopCPUUsage,
                '_top_memory_usage' => $decodedResponseTopMemoryUsage,
                '_devices' => $decodedResponseDevices,
                '_clients' => $decodedResponseClients,
                '_overviewDiagram' => $decodedResponseOverviewDiagram,
                '_trafficActivities' => $decodedResponseTrafficActivities,
            ]);
        }

        $formattedStartDate = Dater::humanReadableDateWithDay($startDate);
        $formattedEndDate = Dater::humanReadableDateWithDay($endDate);
        $formattedAcceptanceDate = Dater::humanReadableDateWithDay($request->acceptanceDate);

        if ($request->site === 'all') {

            return PDFer::exportGeneralDataIntoPDF(
                $dataHolder,
                $formattedStartDate,
                $formattedEndDate,
                $request->project,
                $request->supplier,
                $formattedAcceptanceDate,
                $request->people,
                mergeCombinedStats($dataHolder)
            );
        } else {

            $singleSiteDataHolder = [];

            foreach ($dataHolder as $key => $value) {
                if ($value['_siteId'] === $request->site) {
                    array_push($singleSiteDataHolder, $dataHolder[$key]);
                }
            }

            return PDFer::exportGeneralDataIntoPDF(
                $singleSiteDataHolder,
                $formattedStartDate,
                $formattedEndDate,
                $request->project,
                $request->supplier,
                $formattedAcceptanceDate,
                $request->people,
                mergeCombinedStats($dataHolder)
            );
        }

        // return response()->json(mergeCombinedStats($dataHolder));

        // return response()->json([
        //     'site' => $request->site,
        //     'request_start_date' => $request->startDate,
        //     'converted_start_date_to_unix' => Carbon::parse($request->startDate, 'UTC')->timestamp,
        //     'converted_end_date_to_unix' => Carbon::parse($request->endDate, 'UTC')->timestamp,
        //     'start_date' => $startDate,
        //     'end_date' => $endDate,
        //     'all_dates' => $allDates,
        //     'all_date_keys' => $allDateKeys,
        //     'offline_days' => $offlineDays,
        //     'date_map' => $dateMap,
        //     'days_in_first_month' => $daysInFirstMonth,
        //     'totalDaysWithData' => $totalDaysWithData,
        //     'availability_percent' => $availabilityPercent . '%',
        //     'totalTx' => $totalTx,
        //     'totalDx' => $totalDx,
        //     'upload average speed' => $uploadAvgSpeed,
        //     'download average speed' => $downloadAvgSpeed,
        // ]);
    });

    // END OF FROM OMADA API ===============================================================================================================================

    // random functions

    Route::get('/statistics/{siteId}', function ($siteId){
        return view('sample-frontend.statistics', [
            'item' => Sites::where('siteId', $siteId)->first()
        ]);
    });

    Route::get('/devices/{siteId}', function ($siteId){
        return view('sample-frontend.devices', [
            'item' => Sites::where('siteId', $siteId)->first()
        ]);
    });

    Route::get('/clients/{siteId}', function ($siteId){
        return view('sample-frontend.clients', [
            'item' => Sites::where('siteId', $siteId)->first()
        ]);
    });

    Route::get('/insights/{siteId}', function ($siteId){
        return view('sample-frontend.insights', [
            'item' => Sites::where('siteId', $siteId)->first()
        ]);
    });

    Route::get('/logs/{siteId}', function ($siteId){
        return view('sample-frontend.logs', [
            'item' => Sites::where('siteId', $siteId)->first()
        ]);
    });

    // end...

    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
    Route::get('/create-logs', [LogsController::class, 'create'])->name('logs.create');
    Route::get('/edit-logs/{logsId}', [LogsController::class, 'edit'])->name('logs.edit');
    Route::get('/show-logs/{logsId}', [LogsController::class, 'show'])->name('logs.show');
    Route::get('/delete-logs/{logsId}', [LogsController::class, 'delete'])->name('logs.delete');
    Route::get('/destroy-logs/{logsId}', [LogsController::class, 'destroy'])->name('logs.destroy');
    Route::post('/store-logs', [LogsController::class, 'store'])->name('logs.store');
    Route::post('/update-logs/{logsId}', [LogsController::class, 'update'])->name('logs.update');
    Route::post('/delete-all-bulk-data', [LogsController::class, 'bulkDelete']);

    // Logs Search
    Route::get('/logs-search', [LogsController::class, 'logSearch']);

    // Logs Paginate
    Route::get('/logs-paginate', [LogsController::class, 'logPaginate']);

    // Logs Filter
    Route::get('/logs-filter', [LogsController::class, 'logFilter']);

    // end...

    Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('/create-customers', [CustomersController::class, 'create'])->name('customers.create');
    Route::get('/edit-customers/{customersId}', [CustomersController::class, 'edit'])->name('customers.edit');
    Route::get('/show-customers/{customersId}', [CustomersController::class, 'show'])->name('customers.show');
    Route::get('/delete-customers/{customersId}', [CustomersController::class, 'delete'])->name('customers.delete');
    Route::get('/destroy-customers/{customersId}', [CustomersController::class, 'destroy'])->name('customers.destroy');
    Route::post('/store-customers', [CustomersController::class, 'store'])->name('customers.store');
    Route::post('/update-customers/{customersId}', [CustomersController::class, 'update'])->name('customers.update');
    Route::post('/customers-delete-all-bulk-data', [CustomersController::class, 'bulkDelete']);
    Route::post('/customers-move-to-trash-all-bulk-data', [CustomersController::class, 'bulkMoveToTrash']);
    Route::post('/customers-restore-all-bulk-data', [CustomersController::class, 'bulkRestore']);
    Route::get('/trash-customers', [CustomersController::class, 'trash']);
    Route::get('/restore-customers/{customersId}', [CustomersController::class, 'restore'])->name('customers.restore');

    // Customers Search
    Route::get('/customers-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $customers = Customers::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('customers.customers', compact('customers', 'search'));
    });

    // Customers Paginate
    Route::get('/customers-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the customers based on the 'paginate' value
        $customers = Customers::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated customers
        return view('customers.customers', compact('customers'));
    });

    // Customers Filter
    Route::get('/customers-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for customers
        $query = Customers::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $customers = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $customers = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $customers = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all customers without filtering
            $customers = $query->paginate(10);  // Paginate results
        }

        // Return the view with customers and the selected date range
        return view('customers.customers', compact('customers', 'from', 'to'));
    });

    // end...

    Route::get('/uploads', [UploadsController::class, 'index'])->name('uploads.index');
    Route::get('/create-uploads', [UploadsController::class, 'create'])->name('uploads.create');
    Route::get('/edit-uploads/{uploadsId}', [UploadsController::class, 'edit'])->name('uploads.edit');
    Route::get('/show-uploads/{uploadsId}', [UploadsController::class, 'show'])->name('uploads.show');
    Route::get('/delete-uploads/{uploadsId}', [UploadsController::class, 'delete'])->name('uploads.delete');
    Route::get('/destroy-uploads/{uploadsId}', [UploadsController::class, 'destroy'])->name('uploads.destroy');
    Route::post('/store-uploads', [UploadsController::class, 'store'])->name('uploads.store');
    Route::post('/update-uploads/{uploadsId}', [UploadsController::class, 'update'])->name('uploads.update');
    Route::post('/uploads-delete-all-bulk-data', [UploadsController::class, 'bulkDelete']);
    Route::post('/uploads-move-to-trash-all-bulk-data', [UploadsController::class, 'bulkMoveToTrash']);
    Route::post('/uploads-restore-all-bulk-data', [UploadsController::class, 'bulkRestore']);
    Route::get('/trash-uploads', [UploadsController::class, 'trash']);
    Route::get('/restore-uploads/{uploadsId}', [UploadsController::class, 'restore'])->name('uploads.restore');

    // Uploads Search
    Route::get('/uploads-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $uploads = Uploads::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('uploads.uploads', compact('uploads', 'search'));
    });

    // Uploads Paginate
    Route::get('/uploads-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the uploads based on the 'paginate' value
        $uploads = Uploads::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated uploads
        return view('uploads.uploads', compact('uploads'));
    });

    // Uploads Filter
    Route::get('/uploads-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for uploads
        $query = Uploads::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $uploads = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $uploads = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $uploads = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all uploads without filtering
            $uploads = $query->paginate(10);  // Paginate results
        }

        // Return the view with uploads and the selected date range
        return view('uploads.uploads', compact('uploads', 'from', 'to'));
    });

    // end...

    Route::get('/sites', [SitesController::class, 'index'])->name('sites.index');
    Route::get('/create-sites', [SitesController::class, 'create'])->name('sites.create');
    Route::get('/edit-sites/{sitesId}', [SitesController::class, 'edit'])->name('sites.edit');
    Route::get('/show-sites/{sitesId}', [SitesController::class, 'show'])->name('sites.show');
    Route::get('/delete-sites/{sitesId}', [SitesController::class, 'delete'])->name('sites.delete');
    Route::get('/destroy-sites/{sitesId}', [SitesController::class, 'destroy'])->name('sites.destroy');
    Route::post('/store-sites', [SitesController::class, 'store'])->name('sites.store');
    Route::post('/update-sites/{sitesId}', [SitesController::class, 'update'])->name('sites.update');
    Route::post('/sites-delete-all-bulk-data', [SitesController::class, 'bulkDelete']);
    Route::post('/sites-move-to-trash-all-bulk-data', [SitesController::class, 'bulkMoveToTrash']);
    Route::post('/sites-restore-all-bulk-data', [SitesController::class, 'bulkRestore']);
    Route::get('/trash-sites', [SitesController::class, 'trash']);
    Route::get('/restore-sites/{sitesId}', [SitesController::class, 'restore'])->name('sites.restore');

    // Sites Search
    Route::get('/sites-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $sites = Sites::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('sites.sites', compact('sites', 'search'));
    });

    // Sites Paginate
    Route::get('/sites-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the sites based on the 'paginate' value
        $sites = Sites::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated sites
        return view('sites.sites', compact('sites'));
    });

    // Sites Filter
    Route::get('/sites-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for sites
        $query = Sites::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $sites = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $sites = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $sites = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all sites without filtering
            $sites = $query->paginate(10);  // Paginate results
        }

        // Return the view with sites and the selected date range
        return view('sites.sites', compact('sites', 'from', 'to'));
    });

    // end...

    Route::get('/auditlogs', [AuditlogsController::class, 'index'])->name('auditlogs.index');
    Route::get('/create-auditlogs', [AuditlogsController::class, 'create'])->name('auditlogs.create');
    Route::get('/edit-auditlogs/{auditlogsId}', [AuditlogsController::class, 'edit'])->name('auditlogs.edit');
    Route::get('/show-auditlogs/{auditlogsId}', [AuditlogsController::class, 'show'])->name('auditlogs.show');
    Route::get('/delete-auditlogs/{auditlogsId}', [AuditlogsController::class, 'delete'])->name('auditlogs.delete');
    Route::get('/destroy-auditlogs/{auditlogsId}', [AuditlogsController::class, 'destroy'])->name('auditlogs.destroy');
    Route::post('/store-auditlogs', [AuditlogsController::class, 'store'])->name('auditlogs.store');
    Route::post('/update-auditlogs/{auditlogsId}', [AuditlogsController::class, 'update'])->name('auditlogs.update');
    Route::post('/auditlogs-delete-all-bulk-data', [AuditlogsController::class, 'bulkDelete']);
    Route::post('/auditlogs-move-to-trash-all-bulk-data', [AuditlogsController::class, 'bulkMoveToTrash']);
    Route::post('/auditlogs-restore-all-bulk-data', [AuditlogsController::class, 'bulkRestore']);
    Route::get('/trash-auditlogs', [AuditlogsController::class, 'trash']);
    Route::get('/restore-auditlogs/{auditlogsId}', [AuditlogsController::class, 'restore'])->name('auditlogs.restore');

    // Auditlogs Search
    Route::get('/auditlogs-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $auditlogs = Auditlogs::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('auditlogs.auditlogs', compact('auditlogs', 'search'));
    });

    // Auditlogs Paginate
    Route::get('/auditlogs-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the auditlogs based on the 'paginate' value
        $auditlogs = Auditlogs::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated auditlogs
        return view('auditlogs.auditlogs', compact('auditlogs'));
    });

    // Auditlogs Filter
    Route::get('/auditlogs-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for auditlogs
        $query = Auditlogs::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $auditlogs = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $auditlogs = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $auditlogs = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all auditlogs without filtering
            $auditlogs = $query->paginate(10);  // Paginate results
        }

        // Return the view with auditlogs and the selected date range
        return view('auditlogs.auditlogs', compact('auditlogs', 'from', 'to'));
    });

    // end...


});
