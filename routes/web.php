<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// end of import

use App\Http\Controllers\LogsController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\DICTMiddleware;
use App\Models\Logs;
use Smark\Smark\Cache;

use Illuminate\Support\Facades\Artisan;

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
use Illuminate\Support\Facades\DB;
use Smark\Smark\Dater;
use Smark\Smark\JSON;
use Smark\Smark\PDFer;
use App\Models\User;

// end of import

use App\Http\Controllers\TicketsController;
use App\Models\Tickets;
use OwenIt\Auditing\Models\Audit;

// end of import

use App\Http\Controllers\UseraccountsController;
use App\Models\Useraccounts;

// end of import

use App\Http\Controllers\IncidentsController;
use App\Models\Incidents;

// end of import

use App\Http\Controllers\RestorationsController;
use App\Models\Restorations;
use App\Jobs\RestorationMailSender;
use App\Jobs\RestoredDeviceIncidentMailSender;
use App\Jobs\ReportIncidentMailSender;
use App\Jobs\ReportDisconnectedDeviceMailSender;

// end of import

use App\Http\Controllers\DisconnecteddevicesController;
use App\Models\Disconnecteddevices;

// end of import

use App\Http\Controllers\RestoreddevicesController;
use App\Models\Restoreddevices;

// end of import

use App\Http\Controllers\DevicesController;
use App\Models\Devices;

// end of import

use App\Http\Controllers\ClientsController;
use App\Models\Clients;

// end of import

use App\Http\Controllers\ClientstatsController;
use App\Models\Clientstats;

// end of import

use App\Http\Controllers\OverviewdiagramsController;
use App\Models\Overviewdiagrams;

// end of import

use App\Http\Controllers\TopcpuusagesController;
use App\Models\Topcpuusages;

// end of import

use App\Http\Controllers\ClientdetailsController;
use App\Models\Clientdetails;

// end of import

use App\Http\Controllers\LognotificationsController;
use App\Models\Lognotifications;

// end of import

use App\Http\Controllers\BatchesController;
use App\Models\Batches;

// end of import







function deviceToTarget()
{
    return "apTrafficActivities";
}

function deviceToTargets()
{
    return ["apTrafficActivities", "switchTrafficActivities"];
}

/**
 * Fetch Omada traffic‑activities data, optionally impute gaps, and generate a requested number of
 * synthetic copies (…2, …3, …n).  Synthetic rows inherit the original timestamp and *only* contain
 * randomised data when the original row has numeric values; if the original has **no** data for a
 * field at that timestamp, the synthetic copies will also have **no** data (null) for that field.
 */
function trafficDataImputator(
    $start,
    $end,
    $siteId,
    bool $enableImputation = false,
    int $syntheticCopies = 2
) {
    /* ------------------------------------------------------------------
     | 1️⃣  Retrieve latest access token and query the Omada API          |
     ------------------------------------------------------------------ */
    $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer AccessToken=' . $latestAccessToken,
    ])->withOptions([
        'verify' => false,
    ])->get(
        env('OMADAC_SERVER') .
        "/openapi/v1/" . env('OMADAC_ID') .
        "/sites/{$siteId}/dashboard/traffic-activities?start={$start}&end={$end}"
    );

    $data = json_decode($response->body(), true);
    if (!isset($data['result']) || !is_array($data['result'])) {
        return $data; // unexpected payload ➜ hand upstream
    }

    /* ------------------------------------------------------------------
     | 2️⃣  Utility helpers                                               |
     ------------------------------------------------------------------ */
    $jitter = static function (float $base): float {
        return round($base * (0.9 + mt_rand() / mt_getrandmax() * 0.2), 2);
    };

    /* ------------------------------------------------------------------
     | 3️⃣  Optional imputation on primary series                         |
     ------------------------------------------------------------------ */
    if ($enableImputation) {
        foreach (deviceToTargets() as $targetKey) {
            if (empty($data['result'][$targetKey]) || !is_array($data['result'][$targetKey])) {
                continue;
            }

            $traffic = &$data['result'][$targetKey];
            $sumTx = $sumDx = $cntTx = $cntDx = 0;
            foreach ($traffic as $item) {
                if (isset($item['txData']) && is_numeric($item['txData'])) {
                    $sumTx += $item['txData'];
                    $cntTx++;
                }
                if (isset($item['dxData']) && is_numeric($item['dxData'])) {
                    $sumDx += $item['dxData'];
                    $cntDx++;
                }
            }

            $avgTx = $cntTx ? $sumTx / $cntTx : 1.0;
            $avgDx = $cntDx ? $sumDx / $cntDx : 1.0;

            foreach ($traffic as &$item) {
                if (!isset($item['txData']) || !is_numeric($item['txData'])) {
                    $item['txData'] = $jitter($avgTx);
                }
                if (!isset($item['dxData']) || !is_numeric($item['dxData'])) {
                    $item['dxData'] = $jitter($avgDx);
                }
            }
            unset($item);
        }
    }

    /* ------------------------------------------------------------------
     | 4️⃣  Build synthetic series                                        |
     ------------------------------------------------------------------ */
    foreach (deviceToTargets() as $targetKey) {
        if (empty($data['result'][$targetKey]) || !is_array($data['result'][$targetKey])) {
            continue; // no base data ➜ skip
        }

        // Pre‑compute base averages for jittering.
        $sumTx = $sumDx = 0.0;
        $count  = count($data['result'][$targetKey]);
        foreach ($data['result'][$targetKey] as $row) {
            $sumTx += isset($row['txData']) && is_numeric($row['txData']) ? $row['txData'] : 0;
            $sumDx += isset($row['dxData']) && is_numeric($row['dxData']) ? $row['dxData'] : 0;
        }
        $avgTx = $count ? $sumTx / $count : 1.0;
        $avgDx = $count ? $sumDx / $count : 1.0;

        // Create N synthetic copies (…2, …3, …n+1)
        for ($copy = 1; $copy <= $syntheticCopies; $copy++) {
            $copyKey = $targetKey . ($copy + 1);
            $synthetic = [];

            foreach ($data['result'][$targetKey] as $row) {
                $synthetic[] = [
                    'time' => $row['time'] ?? null,
                    // Only jitter when the original has numeric data; otherwise leave null.
                    'txData' => (isset($row['txData']) && is_numeric($row['txData'])) ? $jitter($avgTx) : null,
                    'dxData' => (isset($row['dxData']) && is_numeric($row['dxData'])) ? $jitter($avgDx) : null,
                ];
            }
            $data['result'][$copyKey] = $synthetic;
        }
    }

    return $data;
}

Route::get('/', function (Request $request) {
    return redirect('/dashboard');
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

    Route::get('/access-logs', function () {
        $logs = DB::table('page_visits')
        ->orderByDesc('visited_at') // or 'created_at' depending on your column
        ->paginate(10); // Change 10 to your preferred per-page count

        // return view('your-view-file', compact('logs'));
        return view('log-page-view.log-page-view', [
            'logs' => $logs
        ]);
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware(AuthMiddleware::class);

    Route::get('/admin-dashboard', function () {
        return view('admin-dashboard');
    })->middleware(AdminMiddleware::class);

    Route::get('/dict-dashboard', function () {
        return view('dict-dashboard');
    })->middleware(DICTMiddleware::class);

    // Access Token

    // FROM OMADA API ===============================================================================================================================

    /************************************************************************ */
    // ON DATABASE
    /************************************************************************ */

    Route::get('/clients-api/{siteId}', function ($siteId) {
        $latestBatch = Batches::latest('id')->first();

        return response()->json([
            'clients' => Clients::where('siteId', $siteId)
                                ->where('batch_number', $latestBatch?->batch_number)
                                ->get(),
            'clientStat' => Clientstats::where('siteId', $siteId)
                                    ->where('batch_number', $latestBatch?->batch_number)
                                    ->get(),
        ]);
    });

    Route::get('/client-details-api/{siteId}/{macAddress}', function ($siteId, $macAddress) {
        $latestBatch = Batches::latest('id')->first();

        return response()->json([
            'errorCode' => 0,
            'result' => Clientdetails::where('mac', $macAddress)
                                    ->where('siteId', $siteId)
                                    ->where('batch_number', $latestBatch?->batch_number)
                                    ->first(),
        ]);
    });

    Route::get('/devices-api/{siteId}', function ($siteId) {
        $latestBatch = Batches::latest('id')->first();

        return response()->json(
            Devices::where('siteId', $siteId)
                ->where('batch_number', $latestBatch?->batch_number)
                ->get()
        );
    });

    Route::get('/log-notification-api/{siteId}', function ($siteId) {
        $latestBatch = Batches::latest('id')->first();

        return response()->json([
            'errorCode' => 0,
            'result' => [
                'logNotifications' => Lognotifications::where('siteId', $siteId)
                                                    ->where('batch_number', $latestBatch?->batch_number)
                                                    ->orderBy('id', 'desc')
                                                    ->get()
            ]
        ]);
    });

    Route::get('/overview-diagram-api/{siteId}', function ($siteId) {
        $latestBatch = Batches::latest('id')->first();

        return response()->json(
            Overviewdiagrams::where('siteId', $siteId)
                            ->where('batch_number', $latestBatch?->batch_number)
                            ->get()
        );
    });

    /************************************************************************ */
    // ON REALTIME
    /************************************************************************ */

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

    Route::get('/traffic-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {
        return response()->json(trafficDataImputator($start, $end, $siteId));
    });

    Route::get('/traffic-api-access-token', function () {
        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];
        return response()->json($latestAccessToken);
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

    // chopped data ===========================

    // bandwidth usage

    Route::get('/get-bandwidth-usage-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {

        function formatBandwidthSpeedGetBandwidthUsageApi($bps)
        {
            $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
            // $units = ['bps', 'Mbps', 'Mbps', 'Gbps', 'Tbps'];
            $i = 0;
            while ($bps >= 1000 && $i < count($units) - 1) {
                $bps /= 1000;
                $i++;
            }
            return number_format($bps, 2) . ' ' . $units[$i];
        }

        $decodedResponseTrafficActivities = trafficDataImputator($start, $end, $siteId);

        // average bandwidth speed -------------------------------------------------------------------------------------------------------

        $totalTx = 0;
        $totalDx = 0;

        $trafficItems = $decodedResponseTrafficActivities['result'][deviceToTarget()] ?? [];

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

        $decodedResponseTrafficActivities = trafficDataImputator($start, $end, $siteId);

        $trafficData = $decodedResponseTrafficActivities['result'][deviceToTarget()] ?? [];

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

        $decodedResponse = trafficDataImputator($start, $end, $siteId);
        $trafficData = $decodedResponse['result'][deviceToTarget()] ?? [];

        // Step 1: Map online days based on txData/rxData
        $dateMap = []; // Tracks which dates had traffic

        foreach ($trafficData as $item) {
            if (!empty($item['txData']) || !empty($item['rxData'])) {
                $dateKey = gmdate('Y-m-d', $item['time']); // UTC date string
                $dateMap[$dateKey] = true;
            }
        }

        // Step 2: Generate all dates between start and end
        $startDate = Carbon::createFromTimestamp($start)->startOfDay();
        $endDate = Carbon::createFromTimestamp($end)->endOfDay();
        $allDates = CarbonPeriod::create($startDate, $endDate);

        $allDateKeys = [];
        foreach ($allDates as $date) {
            $allDateKeys[] = $date->format('Y-m-d');
        }

        // Step 3: Identify offline days
        $offlineDays = 0;
        $missingDates = [];
        $currentRange = [];

        foreach ($allDateKeys as $date) {
            if (!isset($dateMap[$date])) {
                $offlineDays++;
                $currentRange[] = $date;
            } else {
                if (!empty($currentRange)) {
                    $missingDates[] = $currentRange;
                    $currentRange = [];
                }
            }
        }

        if (!empty($currentRange)) {
            $missingDates[] = $currentRange;
        }

        // Step 4: Format missing date ranges
        $formattedRanges = [];
        foreach ($missingDates as $range) {
            $startOff = date('M d Y', strtotime($range[0]));
            $endOff = date('M d Y', strtotime(end($range)));
            $formattedRanges[] = ($startOff === $endOff) ? $startOff : "$startOff - $endOff";
        }

        // Step 5: Calculate accurate availability percentage
        $totalDays = count($allDateKeys);
        $onlineDays = $totalDays - $offlineDays;
        $availabilityPercent = $totalDays > 0 ? round(($onlineDays / $totalDays) * 100) : 0;

        return response()->json($availabilityPercent);

        // end of percentage availability -------------------------------------------------------------------------------------------

    });

    Route::get('/get-traffic-distribution/{start}/{end}/{siteId}', function ($start, $end, $siteId) {

        $client = new \GuzzleHttp\Client();
        $omadacId = env('OMADAC_ID');

        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];

        try {
            $res = $client->request('GET', env('OMADAC_SERVER') . "/openapi/v1/{$omadacId}/sites/{$siteId}/dashboard/traffic-distribution?start=".$start."&end=".$end, [
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

    Route::post('/restore-offline-site/{siteId}', function (Request $request, $siteId) {
        // remove from incidents
        // add to restored

        $request->validate([
            'reason' => 'required',
            'troubleshoot' => 'required',
            'ticket_number' => '',
        ]);

        Incidents::where('siteId', $siteId)->delete();
        Restorations::create([
            'name' => Sites::where('siteId', $siteId)->value('name'),
            'ticket_number' => $request->ticket_number,
            'siteId' => $siteId,
            'time' => new Datetime(now()),
            'reason' => $request->reason,
            'troubleshoot' => $request->troubleshoot,
        ]);

        RestorationMailSender::dispatch(
            Sites::where('siteId', $siteId)->value('name'),
            $request->ticket_number,
            $siteId,
            now()->timezone('Asia/Manila')->format('F j, Y \a\t g:i A'),
            $request->reason,
            $request->troubleshoot,
        );

        return back()->with('success', 'Site is restored!');
    });

    Route::post('/report-offline-site/{siteId}', function (Request $request, $siteId) {
        // remove from incidents
        // add to restored

        $request->validate([
            'reason' => 'required',
            'troubleshoot' => 'required',
        ]);

        Incidents::where('siteId', $siteId)->update([
            'isReported' => 1
        ]);

        ReportIncidentMailSender::dispatch(
            Sites::where('siteId', $siteId)->value('name'),
            $siteId,
            now()->timezone('Asia/Manila')->format('F j, Y \a\t g:i A'),
            $request->reason,
            $request->troubleshoot,
        );

        return back()->with('success', 'Site issue is reported!');
    });

    Route::post('/restore-disconnected-device/{siteId}', function (Request $request, $siteId) {
        // remove from incidents
        // add to restored

        $request->validate([
            'reason' => 'required',
            'troubleshoot' => 'required',
            'ticket_number' => '',
        ]);

        $deviceName = Disconnecteddevices::where('siteId', $siteId)->value('device_name');
        $deviceMac = Disconnecteddevices::where('siteId', $siteId)->value('device_mac');
        $deviceType = Disconnecteddevices::where('siteId', $siteId)->value('device_type');

        Disconnecteddevices::where('siteId', $siteId)->delete();
        Restoreddevices::create([
            'name' => Sites::where('siteId', $siteId)->value('name'),
            'device_name' => $deviceName,
            'device_mac' => $deviceMac,
            'device_type' => $deviceType,
            'status' => 'online',
            'siteId' => $siteId,
            'ticket_number' => $request->ticket_number,
            'reason' => $request->reason,
            'troubleshoot' => $request->troubleshoot,
        ]);

        RestoredDeviceIncidentMailSender::dispatch(
            Sites::where('siteId', $siteId)->value('name'),
            $deviceName,
            $deviceMac,
            $deviceType,
            'online',
            $siteId,
            $request->ticket_number,
            $request->reason,
            $request->troubleshoot,
        );

        return back()->with('success', 'Device is connected!');
    });

    Route::post('/report-disconnected-device/{siteId}', function (Request $request, $siteId) {
        // remove from incidents
        // add to restored

        $request->validate([
            'reason' => 'required',
            'troubleshoot' => 'required',
        ]);

        $deviceName = Disconnecteddevices::where('siteId', $siteId)->value('device_name');
        $deviceMac = Disconnecteddevices::where('siteId', $siteId)->value('device_mac');
        $deviceType = Disconnecteddevices::where('siteId', $siteId)->value('device_type');

        Disconnecteddevices::where('siteId', $siteId)->update([
            'isReported' => 1
        ]);

        ReportDisconnectedDeviceMailSender::dispatch(
            Sites::where('siteId', $siteId)->value('name'),
            $deviceName,
            $deviceMac,
            $deviceType,
            'offline',
            $siteId,
            $request->reason,
            $request->troubleshoot,
        );

        return back()->with('success', 'Device issue is reported!');
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
        // $startTimestamp = Carbon::parse($request->startDate)->timestamp;
        // $endTimestamp = Carbon::parse($request->endDate)->timestamp;
        $startTimestamp = Carbon::parse($request->startDate)->startOfDay()->timestamp;
        $endTimestamp = Carbon::parse($request->endDate)->endOfDay()->timestamp;
        // $startTimestamp = Carbon::parse($request->startDate)->startOfDay()->timestamp;
        // $endTimestamp = Carbon::parse($request->endDate)->endOfDay()->timestamp;

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
            // $units = ['bps', 'Mbps', 'Mbps', 'Gbps', 'Tbps'];
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

            $decodedResponseTrafficActivities = trafficDataImputator(Carbon::parse($request->startDate)->startOfDay()->timestamp, Carbon::parse($request->endDate)->addDays(1)->timestamp, $value['siteId']);

            // average bandwidth speed -------------------------------------------------------------------------------------------------------

            $totalTx = 0;
            $totalDx = 0;

            // $trafficItems = $decodedResponseTrafficActivities['result'][deviceToTarget()] ?? [];

            $trafficItems = [];
            $baseKey = deviceToTarget();
            for ($i = 1; $i <= 3; $i++) {
                $key = $i === 1 ? $baseKey : $baseKey . $i;
                $trafficItems = array_merge($trafficItems, $decodedResponseTrafficActivities['result'][$key] ?? []);
            }

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
            $trafficData = $decodedResponseTrafficActivities['result'][deviceToTarget()];
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
            $startDate = Carbon::createFromTimestamp(Carbon::parse($request->startDate)->startOfDay()->timestamp)->startOfDay();
            $endDate = Carbon::createFromTimestamp(Carbon::parse($request->endDate)->endOfDay()->timestamp)->endOfDay();
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
                    '_start_date' => Carbon::parse($request->startDate)->startOfDay()->timestamp,
                    '_end_date' => Carbon::parse($request->endDate)->endOfDay()->timestamp,
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
        $downloadDate = date('F j, Y');

        if ($request->site === 'all') {

            return PDFer::exportGeneralDataIntoPDF(
                $dataHolder,
                $formattedStartDate,
                $formattedEndDate,
                $request->project,
                $request->supplier,
                $formattedAcceptanceDate,
                $request->people,
                mergeCombinedStats($dataHolder),
                $downloadDate
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
                mergeCombinedStats($dataHolder),
                $downloadDate
            );
        }

        // return response()->json(mergeCombinedStats($dataHolder));

        // return response()->json([
        //     'site' => $request->site,
        //     'request_start_date' => $request->startDate,
        //     'converted_start_date_to_unix' => Carbon::parse($request->startDate)->startOfDay()->timestamp,
        //     'converted_end_date_to_unix' => Carbon::parse($request->endDate)->endOfDay()->timestamp,
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

    Route::get('/tickets/{siteId}', function ($siteId){
        return view('sample-frontend.tickets', [
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

    Route::get('/tickets-api/{siteId}', function ($siteId) {
        $tickets = Tickets::where('sites_id', $siteId)->get();

        return response()->json($tickets);
    });
    Route::get('/tickets', [TicketsController::class, 'index'])->name('tickets.index');
    Route::get('/create-tickets', [TicketsController::class, 'create'])->name('tickets.create');
    Route::get('/edit-tickets/{ticketsId}', [TicketsController::class, 'edit'])->name('tickets.edit');
    Route::get('/show-tickets/{ticketsId}', [TicketsController::class, 'show'])->name('tickets.show');
    Route::get('/delete-tickets/{ticketsId}', [TicketsController::class, 'delete'])->name('tickets.delete');
    Route::get('/destroy-tickets/{ticketsId}', [TicketsController::class, 'destroy']);
    Route::post('/store-tickets', [TicketsController::class, 'store'])->name('tickets.store');
    Route::post('/update-tickets/{ticketsId}', [TicketsController::class, 'update']);
    Route::post('/tickets-delete-all-bulk-data', [TicketsController::class, 'bulkDelete']);
    Route::post('/tickets-move-to-trash-all-bulk-data', [TicketsController::class, 'bulkMoveToTrash']);
    Route::post('/tickets-restore-all-bulk-data', [TicketsController::class, 'bulkRestore']);
    Route::get('/trash-tickets', [TicketsController::class, 'trash']);
    Route::get('/restore-tickets/{ticketsId}', [TicketsController::class, 'restore'])->name('tickets.restore');

    Route::get('/ticket-audits', function () {
        $audits = Audit::latest()->paginate(10);

        return view('sample-frontend.ticket-audits', [
            'audits' => $audits]);
    });

    // Tickets Search
    Route::get('/tickets-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $tickets = Tickets::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('tickets.tickets', compact('tickets', 'search'));
    });

    // Tickets Paginate
    Route::get('/tickets-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the tickets based on the 'paginate' value
        $tickets = Tickets::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated tickets
        return view('tickets.tickets', compact('tickets'));
    });

    // Tickets Filter
    Route::get('/tickets-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for tickets
        $query = Tickets::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $tickets = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $tickets = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $tickets = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all tickets without filtering
            $tickets = $query->paginate(10);  // Paginate results
        }

        // Return the view with tickets and the selected date range
        return view('tickets.tickets', compact('tickets', 'from', 'to'));
    });

    // end...

    Route::get('/useraccounts', [UseraccountsController::class, 'index'])->name('useraccounts.index');
    Route::get('/create-useraccounts', [UseraccountsController::class, 'create'])->name('useraccounts.create');
    Route::get('/edit-useraccounts/{useraccountsId}', [UseraccountsController::class, 'edit'])->name('useraccounts.edit');
    Route::get('/show-useraccounts/{useraccountsId}', [UseraccountsController::class, 'show'])->name('useraccounts.show');
    Route::get('/delete-useraccounts/{useraccountsId}', [UseraccountsController::class, 'delete'])->name('useraccounts.delete');
    Route::get('/destroy-useraccounts/{useraccountsId}', [UseraccountsController::class, 'destroy'])->name('useraccounts.destroy');
    Route::post('/store-useraccounts', [UseraccountsController::class, 'store'])->name('useraccounts.store');
    Route::post('/update-useraccounts/{useraccountsId}', [UseraccountsController::class, 'update'])->name('useraccounts.update');
    Route::post('/useraccounts-delete-all-bulk-data', [UseraccountsController::class, 'bulkDelete']);
    Route::post('/useraccounts-move-to-trash-all-bulk-data', [UseraccountsController::class, 'bulkMoveToTrash']);
    Route::post('/useraccounts-restore-all-bulk-data', [UseraccountsController::class, 'bulkRestore']);
    Route::get('/trash-useraccounts', [UseraccountsController::class, 'trash']);
    Route::get('/restore-useraccounts/{useraccountsId}', [UseraccountsController::class, 'restore'])->name('useraccounts.restore');

    // Useraccounts Search
    Route::get('/useraccounts-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $useraccounts = Useraccounts::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('useraccounts.useraccounts', compact('useraccounts', 'search'));
    });

    // Useraccounts Paginate
    Route::get('/useraccounts-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the useraccounts based on the 'paginate' value
        $useraccounts = Useraccounts::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated useraccounts
        return view('useraccounts.useraccounts', compact('useraccounts'));
    });

    // Useraccounts Filter
    Route::get('/useraccounts-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for useraccounts
        $query = Useraccounts::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $useraccounts = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $useraccounts = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $useraccounts = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all useraccounts without filtering
            $useraccounts = $query->paginate(10);  // Paginate results
        }

        // Return the view with useraccounts and the selected date range
        return view('useraccounts.useraccounts', compact('useraccounts', 'from', 'to'));
    });

    // end...

    Route::get('/incidents', [IncidentsController::class, 'index'])->name('incidents.index');
    Route::get('/create-incidents', [IncidentsController::class, 'create'])->name('incidents.create');
    Route::get('/edit-incidents/{incidentsId}', [IncidentsController::class, 'edit'])->name('incidents.edit');
    Route::get('/show-incidents/{incidentsId}', [IncidentsController::class, 'show'])->name('incidents.show');
    Route::get('/delete-incidents/{incidentsId}', [IncidentsController::class, 'delete'])->name('incidents.delete');
    Route::get('/destroy-incidents/{incidentsId}', [IncidentsController::class, 'destroy'])->name('incidents.destroy');
    Route::post('/store-incidents', [IncidentsController::class, 'store'])->name('incidents.store');
    Route::post('/update-incidents/{incidentsId}', [IncidentsController::class, 'update'])->name('incidents.update');
    Route::post('/incidents-delete-all-bulk-data', [IncidentsController::class, 'bulkDelete']);
    Route::post('/incidents-move-to-trash-all-bulk-data', [IncidentsController::class, 'bulkMoveToTrash']);
    Route::post('/incidents-restore-all-bulk-data', [IncidentsController::class, 'bulkRestore']);
    Route::get('/trash-incidents', [IncidentsController::class, 'trash']);
    Route::get('/restore-incidents/{incidentsId}', [IncidentsController::class, 'restore'])->name('incidents.restore');

    // Incidents Search
    Route::get('/incidents-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $incidents = Incidents::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('incidents.incidents', compact('incidents', 'search'));
    });

    // Incidents Paginate
    Route::get('/incidents-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the incidents based on the 'paginate' value
        $incidents = Incidents::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated incidents
        return view('incidents.incidents', compact('incidents'));
    });

    // Incidents Filter
    Route::get('/incidents-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for incidents
        $query = Incidents::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $incidents = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $incidents = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $incidents = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all incidents without filtering
            $incidents = $query->paginate(10);  // Paginate results
        }

        // Return the view with incidents and the selected date range
        return view('incidents.incidents', compact('incidents', 'from', 'to'));
    });

    // end...

    Route::get('/restorations', [RestorationsController::class, 'index'])->name('restorations.index');
    Route::get('/create-restorations', [RestorationsController::class, 'create'])->name('restorations.create');
    Route::get('/edit-restorations/{restorationsId}', [RestorationsController::class, 'edit'])->name('restorations.edit');
    Route::get('/show-restorations/{restorationsId}', [RestorationsController::class, 'show'])->name('restorations.show');
    Route::get('/delete-restorations/{restorationsId}', [RestorationsController::class, 'delete'])->name('restorations.delete');
    Route::get('/destroy-restorations/{restorationsId}', [RestorationsController::class, 'destroy'])->name('restorations.destroy');
    Route::post('/store-restorations', [RestorationsController::class, 'store'])->name('restorations.store');
    Route::post('/update-restorations/{restorationsId}', [RestorationsController::class, 'update'])->name('restorations.update');
    Route::post('/restorations-delete-all-bulk-data', [RestorationsController::class, 'bulkDelete']);
    Route::post('/restorations-move-to-trash-all-bulk-data', [RestorationsController::class, 'bulkMoveToTrash']);
    Route::post('/restorations-restore-all-bulk-data', [RestorationsController::class, 'bulkRestore']);
    Route::get('/trash-restorations', [RestorationsController::class, 'trash']);
    Route::get('/restore-restorations/{restorationsId}', [RestorationsController::class, 'restore'])->name('restorations.restore');

    // Restorations Search
    Route::get('/restorations-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $restorations = Restorations::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('restorations.restorations', compact('restorations', 'search'));
    });

    // Restorations Paginate
    Route::get('/restorations-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the restorations based on the 'paginate' value
        $restorations = Restorations::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated restorations
        return view('restorations.restorations', compact('restorations'));
    });

    // Restorations Filter
    Route::get('/restorations-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for restorations
        $query = Restorations::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $restorations = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $restorations = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $restorations = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all restorations without filtering
            $restorations = $query->paginate(10);  // Paginate results
        }

        // Return the view with restorations and the selected date range
        return view('restorations.restorations', compact('restorations', 'from', 'to'));
    });

    // end...

    Route::get('/disconnecteddevices', [DisconnecteddevicesController::class, 'index'])->name('disconnecteddevices.index');
    Route::get('/create-disconnecteddevices', [DisconnecteddevicesController::class, 'create'])->name('disconnecteddevices.create');
    Route::get('/edit-disconnecteddevices/{disconnecteddevicesId}', [DisconnecteddevicesController::class, 'edit'])->name('disconnecteddevices.edit');
    Route::get('/show-disconnecteddevices/{disconnecteddevicesId}', [DisconnecteddevicesController::class, 'show'])->name('disconnecteddevices.show');
    Route::get('/delete-disconnecteddevices/{disconnecteddevicesId}', [DisconnecteddevicesController::class, 'delete'])->name('disconnecteddevices.delete');
    Route::get('/destroy-disconnecteddevices/{disconnecteddevicesId}', [DisconnecteddevicesController::class, 'destroy'])->name('disconnecteddevices.destroy');
    Route::post('/store-disconnecteddevices', [DisconnecteddevicesController::class, 'store'])->name('disconnecteddevices.store');
    Route::post('/update-disconnecteddevices/{disconnecteddevicesId}', [DisconnecteddevicesController::class, 'update'])->name('disconnecteddevices.update');
    Route::post('/disconnecteddevices-delete-all-bulk-data', [DisconnecteddevicesController::class, 'bulkDelete']);
    Route::post('/disconnecteddevices-move-to-trash-all-bulk-data', [DisconnecteddevicesController::class, 'bulkMoveToTrash']);
    Route::post('/disconnecteddevices-restore-all-bulk-data', [DisconnecteddevicesController::class, 'bulkRestore']);
    Route::get('/trash-disconnecteddevices', [DisconnecteddevicesController::class, 'trash']);
    Route::get('/restore-disconnecteddevices/{disconnecteddevicesId}', [DisconnecteddevicesController::class, 'restore'])->name('disconnecteddevices.restore');

    // Disconnecteddevices Search
    Route::get('/disconnecteddevices-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $disconnecteddevices = Disconnecteddevices::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('disconnecteddevices.disconnecteddevices', compact('disconnecteddevices', 'search'));
    });

    // Disconnecteddevices Paginate
    Route::get('/disconnecteddevices-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the disconnecteddevices based on the 'paginate' value
        $disconnecteddevices = Disconnecteddevices::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated disconnecteddevices
        return view('disconnecteddevices.disconnecteddevices', compact('disconnecteddevices'));
    });

    // Disconnecteddevices Filter
    Route::get('/disconnecteddevices-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for disconnecteddevices
        $query = Disconnecteddevices::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $disconnecteddevices = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $disconnecteddevices = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $disconnecteddevices = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all disconnecteddevices without filtering
            $disconnecteddevices = $query->paginate(10);  // Paginate results
        }

        // Return the view with disconnecteddevices and the selected date range
        return view('disconnecteddevices.disconnecteddevices', compact('disconnecteddevices', 'from', 'to'));
    });

    // end...

    Route::get('/restoreddevices', [RestoreddevicesController::class, 'index'])->name('restoreddevices.index');
    Route::get('/create-restoreddevices', [RestoreddevicesController::class, 'create'])->name('restoreddevices.create');
    Route::get('/edit-restoreddevices/{restoreddevicesId}', [RestoreddevicesController::class, 'edit'])->name('restoreddevices.edit');
    Route::get('/show-restoreddevices/{restoreddevicesId}', [RestoreddevicesController::class, 'show'])->name('restoreddevices.show');
    Route::get('/delete-restoreddevices/{restoreddevicesId}', [RestoreddevicesController::class, 'delete'])->name('restoreddevices.delete');
    Route::get('/destroy-restoreddevices/{restoreddevicesId}', [RestoreddevicesController::class, 'destroy'])->name('restoreddevices.destroy');
    Route::post('/store-restoreddevices', [RestoreddevicesController::class, 'store'])->name('restoreddevices.store');
    Route::post('/update-restoreddevices/{restoreddevicesId}', [RestoreddevicesController::class, 'update'])->name('restoreddevices.update');
    Route::post('/restoreddevices-delete-all-bulk-data', [RestoreddevicesController::class, 'bulkDelete']);
    Route::post('/restoreddevices-move-to-trash-all-bulk-data', [RestoreddevicesController::class, 'bulkMoveToTrash']);
    Route::post('/restoreddevices-restore-all-bulk-data', [RestoreddevicesController::class, 'bulkRestore']);
    Route::get('/trash-restoreddevices', [RestoreddevicesController::class, 'trash']);
    Route::get('/restore-restoreddevices/{restoreddevicesId}', [RestoreddevicesController::class, 'restore'])->name('restoreddevices.restore');

    // Restoreddevices Search
    Route::get('/restoreddevices-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $restoreddevices = Restoreddevices::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('restoreddevices.restoreddevices', compact('restoreddevices', 'search'));
    });

    // Restoreddevices Paginate
    Route::get('/restoreddevices-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the restoreddevices based on the 'paginate' value
        $restoreddevices = Restoreddevices::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated restoreddevices
        return view('restoreddevices.restoreddevices', compact('restoreddevices'));
    });

    // Restoreddevices Filter
    Route::get('/restoreddevices-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for restoreddevices
        $query = Restoreddevices::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $restoreddevices = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $restoreddevices = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $restoreddevices = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all restoreddevices without filtering
            $restoreddevices = $query->paginate(10);  // Paginate results
        }

        // Return the view with restoreddevices and the selected date range
        return view('restoreddevices.restoreddevices', compact('restoreddevices', 'from', 'to'));
    });

    // end...

    Route::get('/devices', [DevicesController::class, 'index'])->name('devices.index');
    Route::get('/create-devices', [DevicesController::class, 'create'])->name('devices.create');
    Route::get('/edit-devices/{devicesId}', [DevicesController::class, 'edit'])->name('devices.edit');
    Route::get('/show-devices/{devicesId}', [DevicesController::class, 'show'])->name('devices.show');
    Route::get('/delete-devices/{devicesId}', [DevicesController::class, 'delete'])->name('devices.delete');
    Route::get('/destroy-devices/{devicesId}', [DevicesController::class, 'destroy'])->name('devices.destroy');
    Route::post('/store-devices', [DevicesController::class, 'store'])->name('devices.store');
    Route::post('/update-devices/{devicesId}', [DevicesController::class, 'update'])->name('devices.update');
    Route::post('/devices-delete-all-bulk-data', [DevicesController::class, 'bulkDelete']);
    Route::post('/devices-move-to-trash-all-bulk-data', [DevicesController::class, 'bulkMoveToTrash']);
    Route::post('/devices-restore-all-bulk-data', [DevicesController::class, 'bulkRestore']);
    Route::get('/trash-devices', [DevicesController::class, 'trash']);
    Route::get('/restore-devices/{devicesId}', [DevicesController::class, 'restore'])->name('devices.restore');

    // Devices Search
    Route::get('/devices-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $devices = Devices::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('devices.devices', compact('devices', 'search'));
    });

    // Devices Paginate
    Route::get('/devices-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the devices based on the 'paginate' value
        $devices = Devices::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated devices
        return view('devices.devices', compact('devices'));
    });

    // Devices Filter
    Route::get('/devices-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for devices
        $query = Devices::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $devices = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $devices = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $devices = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all devices without filtering
            $devices = $query->paginate(10);  // Paginate results
        }

        // Return the view with devices and the selected date range
        return view('devices.devices', compact('devices', 'from', 'to'));
    });

    // end...

    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('/create-clients', [ClientsController::class, 'create'])->name('clients.create');
    Route::get('/edit-clients/{clientsId}', [ClientsController::class, 'edit'])->name('clients.edit');
    Route::get('/show-clients/{clientsId}', [ClientsController::class, 'show'])->name('clients.show');
    Route::get('/delete-clients/{clientsId}', [ClientsController::class, 'delete'])->name('clients.delete');
    Route::get('/destroy-clients/{clientsId}', [ClientsController::class, 'destroy'])->name('clients.destroy');
    Route::post('/store-clients', [ClientsController::class, 'store'])->name('clients.store');
    Route::post('/update-clients/{clientsId}', [ClientsController::class, 'update'])->name('clients.update');
    Route::post('/clients-delete-all-bulk-data', [ClientsController::class, 'bulkDelete']);
    Route::post('/clients-move-to-trash-all-bulk-data', [ClientsController::class, 'bulkMoveToTrash']);
    Route::post('/clients-restore-all-bulk-data', [ClientsController::class, 'bulkRestore']);
    Route::get('/trash-clients', [ClientsController::class, 'trash']);
    Route::get('/restore-clients/{clientsId}', [ClientsController::class, 'restore'])->name('clients.restore');

    // Clients Search
    Route::get('/clients-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $clients = Clients::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('clients.clients', compact('clients', 'search'));
    });

    // Clients Paginate
    Route::get('/clients-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the clients based on the 'paginate' value
        $clients = Clients::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated clients
        return view('clients.clients', compact('clients'));
    });

    // Clients Filter
    Route::get('/clients-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for clients
        $query = Clients::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $clients = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $clients = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $clients = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all clients without filtering
            $clients = $query->paginate(10);  // Paginate results
        }

        // Return the view with clients and the selected date range
        return view('clients.clients', compact('clients', 'from', 'to'));
    });

    // end...

    Route::get('/clientstats', [ClientstatsController::class, 'index'])->name('clientstats.index');
    Route::get('/create-clientstats', [ClientstatsController::class, 'create'])->name('clientstats.create');
    Route::get('/edit-clientstats/{clientstatsId}', [ClientstatsController::class, 'edit'])->name('clientstats.edit');
    Route::get('/show-clientstats/{clientstatsId}', [ClientstatsController::class, 'show'])->name('clientstats.show');
    Route::get('/delete-clientstats/{clientstatsId}', [ClientstatsController::class, 'delete'])->name('clientstats.delete');
    Route::get('/destroy-clientstats/{clientstatsId}', [ClientstatsController::class, 'destroy'])->name('clientstats.destroy');
    Route::post('/store-clientstats', [ClientstatsController::class, 'store'])->name('clientstats.store');
    Route::post('/update-clientstats/{clientstatsId}', [ClientstatsController::class, 'update'])->name('clientstats.update');
    Route::post('/clientstats-delete-all-bulk-data', [ClientstatsController::class, 'bulkDelete']);
    Route::post('/clientstats-move-to-trash-all-bulk-data', [ClientstatsController::class, 'bulkMoveToTrash']);
    Route::post('/clientstats-restore-all-bulk-data', [ClientstatsController::class, 'bulkRestore']);
    Route::get('/trash-clientstats', [ClientstatsController::class, 'trash']);
    Route::get('/restore-clientstats/{clientstatsId}', [ClientstatsController::class, 'restore'])->name('clientstats.restore');

    // Clientstats Search
    Route::get('/clientstats-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $clientstats = Clientstats::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('clientstats.clientstats', compact('clientstats', 'search'));
    });

    // Clientstats Paginate
    Route::get('/clientstats-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the clientstats based on the 'paginate' value
        $clientstats = Clientstats::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated clientstats
        return view('clientstats.clientstats', compact('clientstats'));
    });

    // Clientstats Filter
    Route::get('/clientstats-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for clientstats
        $query = Clientstats::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $clientstats = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $clientstats = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $clientstats = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all clientstats without filtering
            $clientstats = $query->paginate(10);  // Paginate results
        }

        // Return the view with clientstats and the selected date range
        return view('clientstats.clientstats', compact('clientstats', 'from', 'to'));
    });

    // end...

    Route::get('/overviewdiagrams', [OverviewdiagramsController::class, 'index'])->name('overviewdiagrams.index');
    Route::get('/create-overviewdiagrams', [OverviewdiagramsController::class, 'create'])->name('overviewdiagrams.create');
    Route::get('/edit-overviewdiagrams/{overviewdiagramsId}', [OverviewdiagramsController::class, 'edit'])->name('overviewdiagrams.edit');
    Route::get('/show-overviewdiagrams/{overviewdiagramsId}', [OverviewdiagramsController::class, 'show'])->name('overviewdiagrams.show');
    Route::get('/delete-overviewdiagrams/{overviewdiagramsId}', [OverviewdiagramsController::class, 'delete'])->name('overviewdiagrams.delete');
    Route::get('/destroy-overviewdiagrams/{overviewdiagramsId}', [OverviewdiagramsController::class, 'destroy'])->name('overviewdiagrams.destroy');
    Route::post('/store-overviewdiagrams', [OverviewdiagramsController::class, 'store'])->name('overviewdiagrams.store');
    Route::post('/update-overviewdiagrams/{overviewdiagramsId}', [OverviewdiagramsController::class, 'update'])->name('overviewdiagrams.update');
    Route::post('/overviewdiagrams-delete-all-bulk-data', [OverviewdiagramsController::class, 'bulkDelete']);
    Route::post('/overviewdiagrams-move-to-trash-all-bulk-data', [OverviewdiagramsController::class, 'bulkMoveToTrash']);
    Route::post('/overviewdiagrams-restore-all-bulk-data', [OverviewdiagramsController::class, 'bulkRestore']);
    Route::get('/trash-overviewdiagrams', [OverviewdiagramsController::class, 'trash']);
    Route::get('/restore-overviewdiagrams/{overviewdiagramsId}', [OverviewdiagramsController::class, 'restore'])->name('overviewdiagrams.restore');

    // Overviewdiagrams Search
    Route::get('/overviewdiagrams-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $overviewdiagrams = Overviewdiagrams::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('overviewdiagrams.overviewdiagrams', compact('overviewdiagrams', 'search'));
    });

    // Overviewdiagrams Paginate
    Route::get('/overviewdiagrams-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the overviewdiagrams based on the 'paginate' value
        $overviewdiagrams = Overviewdiagrams::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated overviewdiagrams
        return view('overviewdiagrams.overviewdiagrams', compact('overviewdiagrams'));
    });

    // Overviewdiagrams Filter
    Route::get('/overviewdiagrams-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for overviewdiagrams
        $query = Overviewdiagrams::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $overviewdiagrams = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $overviewdiagrams = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $overviewdiagrams = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all overviewdiagrams without filtering
            $overviewdiagrams = $query->paginate(10);  // Paginate results
        }

        // Return the view with overviewdiagrams and the selected date range
        return view('overviewdiagrams.overviewdiagrams', compact('overviewdiagrams', 'from', 'to'));
    });

    // end...

    Route::get('/topcpuusages', [TopcpuusagesController::class, 'index'])->name('topcpuusages.index');
    Route::get('/create-topcpuusages', [TopcpuusagesController::class, 'create'])->name('topcpuusages.create');
    Route::get('/edit-topcpuusages/{topcpuusagesId}', [TopcpuusagesController::class, 'edit'])->name('topcpuusages.edit');
    Route::get('/show-topcpuusages/{topcpuusagesId}', [TopcpuusagesController::class, 'show'])->name('topcpuusages.show');
    Route::get('/delete-topcpuusages/{topcpuusagesId}', [TopcpuusagesController::class, 'delete'])->name('topcpuusages.delete');
    Route::get('/destroy-topcpuusages/{topcpuusagesId}', [TopcpuusagesController::class, 'destroy'])->name('topcpuusages.destroy');
    Route::post('/store-topcpuusages', [TopcpuusagesController::class, 'store'])->name('topcpuusages.store');
    Route::post('/update-topcpuusages/{topcpuusagesId}', [TopcpuusagesController::class, 'update'])->name('topcpuusages.update');
    Route::post('/topcpuusages-delete-all-bulk-data', [TopcpuusagesController::class, 'bulkDelete']);
    Route::post('/topcpuusages-move-to-trash-all-bulk-data', [TopcpuusagesController::class, 'bulkMoveToTrash']);
    Route::post('/topcpuusages-restore-all-bulk-data', [TopcpuusagesController::class, 'bulkRestore']);
    Route::get('/trash-topcpuusages', [TopcpuusagesController::class, 'trash']);
    Route::get('/restore-topcpuusages/{topcpuusagesId}', [TopcpuusagesController::class, 'restore'])->name('topcpuusages.restore');

    // Topcpuusages Search
    Route::get('/topcpuusages-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $topcpuusages = Topcpuusages::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('topcpuusages.topcpuusages', compact('topcpuusages', 'search'));
    });

    // Topcpuusages Paginate
    Route::get('/topcpuusages-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the topcpuusages based on the 'paginate' value
        $topcpuusages = Topcpuusages::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated topcpuusages
        return view('topcpuusages.topcpuusages', compact('topcpuusages'));
    });

    // Topcpuusages Filter
    Route::get('/topcpuusages-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for topcpuusages
        $query = Topcpuusages::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $topcpuusages = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $topcpuusages = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $topcpuusages = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all topcpuusages without filtering
            $topcpuusages = $query->paginate(10);  // Paginate results
        }

        // Return the view with topcpuusages and the selected date range
        return view('topcpuusages.topcpuusages', compact('topcpuusages', 'from', 'to'));
    });

    // end...

    Route::get('/clientdetails', [ClientdetailsController::class, 'index'])->name('clientdetails.index');
    Route::get('/create-clientdetails', [ClientdetailsController::class, 'create'])->name('clientdetails.create');
    Route::get('/edit-clientdetails/{clientdetailsId}', [ClientdetailsController::class, 'edit'])->name('clientdetails.edit');
    Route::get('/show-clientdetails/{clientdetailsId}', [ClientdetailsController::class, 'show'])->name('clientdetails.show');
    Route::get('/delete-clientdetails/{clientdetailsId}', [ClientdetailsController::class, 'delete'])->name('clientdetails.delete');
    Route::get('/destroy-clientdetails/{clientdetailsId}', [ClientdetailsController::class, 'destroy'])->name('clientdetails.destroy');
    Route::post('/store-clientdetails', [ClientdetailsController::class, 'store'])->name('clientdetails.store');
    Route::post('/update-clientdetails/{clientdetailsId}', [ClientdetailsController::class, 'update'])->name('clientdetails.update');
    Route::post('/clientdetails-delete-all-bulk-data', [ClientdetailsController::class, 'bulkDelete']);
    Route::post('/clientdetails-move-to-trash-all-bulk-data', [ClientdetailsController::class, 'bulkMoveToTrash']);
    Route::post('/clientdetails-restore-all-bulk-data', [ClientdetailsController::class, 'bulkRestore']);
    Route::get('/trash-clientdetails', [ClientdetailsController::class, 'trash']);
    Route::get('/restore-clientdetails/{clientdetailsId}', [ClientdetailsController::class, 'restore'])->name('clientdetails.restore');

    // Clientdetails Search
    Route::get('/clientdetails-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $clientdetails = Clientdetails::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('clientdetails.clientdetails', compact('clientdetails', 'search'));
    });

    // Clientdetails Paginate
    Route::get('/clientdetails-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the clientdetails based on the 'paginate' value
        $clientdetails = Clientdetails::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated clientdetails
        return view('clientdetails.clientdetails', compact('clientdetails'));
    });

    // Clientdetails Filter
    Route::get('/clientdetails-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for clientdetails
        $query = Clientdetails::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $clientdetails = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $clientdetails = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $clientdetails = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all clientdetails without filtering
            $clientdetails = $query->paginate(10);  // Paginate results
        }

        // Return the view with clientdetails and the selected date range
        return view('clientdetails.clientdetails', compact('clientdetails', 'from', 'to'));
    });

    // end...

    Route::get('/lognotifications', [LognotificationsController::class, 'index'])->name('lognotifications.index');
    Route::get('/create-lognotifications', [LognotificationsController::class, 'create'])->name('lognotifications.create');
    Route::get('/edit-lognotifications/{lognotificationsId}', [LognotificationsController::class, 'edit'])->name('lognotifications.edit');
    Route::get('/show-lognotifications/{lognotificationsId}', [LognotificationsController::class, 'show'])->name('lognotifications.show');
    Route::get('/delete-lognotifications/{lognotificationsId}', [LognotificationsController::class, 'delete'])->name('lognotifications.delete');
    Route::get('/destroy-lognotifications/{lognotificationsId}', [LognotificationsController::class, 'destroy'])->name('lognotifications.destroy');
    Route::post('/store-lognotifications', [LognotificationsController::class, 'store'])->name('lognotifications.store');
    Route::post('/update-lognotifications/{lognotificationsId}', [LognotificationsController::class, 'update'])->name('lognotifications.update');
    Route::post('/lognotifications-delete-all-bulk-data', [LognotificationsController::class, 'bulkDelete']);
    Route::post('/lognotifications-move-to-trash-all-bulk-data', [LognotificationsController::class, 'bulkMoveToTrash']);
    Route::post('/lognotifications-restore-all-bulk-data', [LognotificationsController::class, 'bulkRestore']);
    Route::get('/trash-lognotifications', [LognotificationsController::class, 'trash']);
    Route::get('/restore-lognotifications/{lognotificationsId}', [LognotificationsController::class, 'restore'])->name('lognotifications.restore');

    // Lognotifications Search
    Route::get('/lognotifications-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $lognotifications = Lognotifications::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('lognotifications.lognotifications', compact('lognotifications', 'search'));
    });

    // Lognotifications Paginate
    Route::get('/lognotifications-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the lognotifications based on the 'paginate' value
        $lognotifications = Lognotifications::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated lognotifications
        return view('lognotifications.lognotifications', compact('lognotifications'));
    });

    // Lognotifications Filter
    Route::get('/lognotifications-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for lognotifications
        $query = Lognotifications::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $lognotifications = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $lognotifications = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $lognotifications = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all lognotifications without filtering
            $lognotifications = $query->paginate(10);  // Paginate results
        }

        // Return the view with lognotifications and the selected date range
        return view('lognotifications.lognotifications', compact('lognotifications', 'from', 'to'));
    });

    // end...

    Route::get('/batches', [BatchesController::class, 'index'])->name('batches.index');
    Route::get('/create-batches', [BatchesController::class, 'create'])->name('batches.create');
    Route::get('/edit-batches/{batchesId}', [BatchesController::class, 'edit'])->name('batches.edit');
    Route::get('/show-batches/{batchesId}', [BatchesController::class, 'show'])->name('batches.show');
    Route::get('/delete-batches/{batchesId}', [BatchesController::class, 'delete'])->name('batches.delete');
    Route::get('/destroy-batches/{batchesId}', [BatchesController::class, 'destroy'])->name('batches.destroy');
    Route::post('/store-batches', [BatchesController::class, 'store'])->name('batches.store');
    Route::post('/update-batches/{batchesId}', [BatchesController::class, 'update'])->name('batches.update');
    Route::post('/batches-delete-all-bulk-data', [BatchesController::class, 'bulkDelete']);
    Route::post('/batches-move-to-trash-all-bulk-data', [BatchesController::class, 'bulkMoveToTrash']);
    Route::post('/batches-restore-all-bulk-data', [BatchesController::class, 'bulkRestore']);
    Route::get('/trash-batches', [BatchesController::class, 'trash']);
    Route::get('/restore-batches/{batchesId}', [BatchesController::class, 'restore'])->name('batches.restore');

    // Batches Search
    Route::get('/batches-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $batches = Batches::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('batches.batches', compact('batches', 'search'));
    });

    // Batches Paginate
    Route::get('/batches-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided

        // Paginate the batches based on the 'paginate' value
        $batches = Batches::paginate($paginate); // Paginate with the specified number of items per page

        // Return the view with the paginated batches
        return view('batches.batches', compact('batches'));
    });

    // Batches Filter
    Route::get('/batches-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');

        // Default query for batches
        $query = Batches::query();

        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $batches = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $batches = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $batches = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all batches without filtering
            $batches = $query->paginate(10);  // Paginate results
        }

        // Return the view with batches and the selected date range
        return view('batches.batches', compact('batches', 'from', 'to'));
    });

    // end...

});
