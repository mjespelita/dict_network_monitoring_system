<?php

namespace App\Http\Controllers;

use App\Models\{Logs};
use App\Http\Requests\StoreLogsRequest;
use App\Http\Requests\UpdateLogsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('logs.logs', [
            'logs' => Logs::orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function logSearch(Request $request)
    {
        $search = $request->get('search');

        // Perform the search logic
        $logs = Logs::when($search, function ($query) use ($search) {
            return $query->where('log', 'like', "%$search%");
        })->paginate(10);

        return view('logs.logs', compact('logs', 'search'));
    }

    public function logPaginate(Request $request)
    {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the logs based on the 'paginate' value
        $logs = Logs::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated logs
        return view('logs.logs', compact('logs'));
    }

    public function logFilter(Request $request)
    {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for logs
        $query = Logs::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $logs = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $logs = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $logs = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all logs without filtering
            $logs = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with logs and the selected date range
        return view('logs.logs', compact('logs', 'from', 'to'));
    }
}