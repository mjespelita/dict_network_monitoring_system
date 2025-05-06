<?php

use App\Models\Auditlogs;
use App\Models\Customers;
use App\Models\Logs;
use App\Models\Sites;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Smark\Smark\Dater;
use Smark\Smark\JSON;

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
                'customerId' => isset($value['customerId']) ? $value['customerId'] : null,
                'customerName' => isset($value['customerName']) ? $value['customerName'] : null,
                'region' => isset($value['region']) ? $value['region'] : null,
                'timezone' => isset($value['timeZone']) ? $value['timeZone'] : null,
                'scenario' => isset($value['scenario']) ? $value['scenario'] : null,
                'wan' => isset($value['wan']) ? $value['wan'] : null,
                'connectedApNum' => isset($value['connectedApNum']) ? $value['connectedApNum'] : null,
                'disconnectedApNum' => isset($value['disconnectedApNum']) ? $value['disconnectedApNum'] : null,
                'isolatedApNum' => isset($value['isolatedApNum']) ? $value['isolatedApNum'] : null,
                'connectedSwitchNum' => isset($value['connectedSwitchNum']) ? $value['connectedSwitchNum'] : null,
                'disconnectedSwitchNum' => isset($value['disconnectedSwitchNum']) ? $value['disconnectedSwitchNum'] : null,
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

})->purpose('Sync data from the API.')->everyTenSeconds();