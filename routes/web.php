<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// end of import

use App\Http\Controllers\LogsController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Models\Logs;

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
use Smark\Smark\JSON;

// end of import


Route::get('/', function () {
    return view('welcome');
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

    Route::get('/traffic-api/{start}/{end}/{siteId}', function ($start, $end, $siteId) {
        
        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer AccessToken='.$latestAccessToken, // Replace with your API key
        ])->withOptions([
            'verify' => false,
        ])->get(env('OMADAC_SERVER').'/openapi/v1/'.env('OMADAC_ID').'/sites/'.$siteId.'/dashboard/traffic-activities?start='.$start.'&end='.$end);

        return json_decode($response->body(), true);
    });

    Route::get('/traffic-api-access-token', function () {
        $latestAccessToken = JSON::jsonRead('accessTokenStorage/accessTokens.json')[0]['accessToken'];
        return response()->json($latestAccessToken);
    });

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
