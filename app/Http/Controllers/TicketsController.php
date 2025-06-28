<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Tickets, User};
use App\Http\Requests\StoreTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Mail\TicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tickets.tickets', [
            'tickets' => Tickets::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('tickets.trash-tickets', [
            'tickets' => Tickets::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($ticketsId)
    {
        /* Log ************************************************** */
        $oldName = Tickets::where('id', $ticketsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Tickets "'.$oldName.'".']);
        /******************************************************** */

        Tickets::where('id', $ticketsId)->update(['isTrash' => '0']);

        return redirect('/tickets');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tickets.create-tickets');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketsRequest $request)
    {

        // Get the latest ticket for the given site
        $latestTicket = Tickets::where('ticket_number', 'like', 'TCKT' . $request->sites_id . '_%')
            ->orderByDesc('id')
            ->first();

        // Determine the next number
        $nextNumber = 1; // default if no existing ticket
        if ($latestTicket && preg_match('/_(\d+)$/', $latestTicket->ticket_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        // Format the new ticket number with 5-digit padding
        $ticketNumber = 'TCKT_' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        Tickets::create([
            'sites_id' => $request->sites_id,
            'ticket_number' => $ticketNumber,
            'date_reported' => $request->date_reported,
            'name' => $request->name,
            'address' => $request->address,
            'nearest_landmark' => $request->nearest_landmark,
            'issue' => $request->issue,
            'troubleshooting' => $request->troubleshooting
        ]);

        $users = User::all();

        foreach ($users as $key => $user) {
            // Mail::to($user->email)->send(new TicketMail(
            //     $request->sites_id,
            //     $request->ticket_number,
            //     $request->date_reported,
            //     $request->name,
            //     $request->address,
            //     $request->nearest_landmark,
            //     $request->issue,
            //     $request->troubleshooting
            // ));
        }

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Tickets '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Tickets Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tickets $tickets, $ticketsId)
    {
        return view('tickets.show-tickets', [
            'item' => Tickets::where('id', $ticketsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tickets $tickets, $ticketsId)
    {
        return view('tickets.edit-tickets', [
            'item' => Tickets::where('id', $ticketsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketsRequest $request, Tickets $tickets, $ticketsId)
    {
        /* Log ************************************************** */
        // $oldName = Tickets::where('id', $ticketsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Tickets from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        // $ticket = Tickets::where('id', $ticketsId)->update([
        //     'sites_id' => $request->sites_id,
        //     'ticket_number' => $request->ticket_number,
        //     'date_reported' => $request->date_reported,
        //     'name' => $request->name,
        //     'address' => $request->address,
        //     'nearest_landmark' => $request->nearest_landmark,
        //     'issue' => $request->issue,
        //     'troubleshooting' => $request->troubleshooting,
        //     'status' => $request->status
        // ]);

        $ticket = Tickets::findOrFail($ticketsId);

        $ticket->sites_id = $request->sites_id;
        $ticket->ticket_number = $request->ticket_number;
        $ticket->date_reported = $request->date_reported;
        $ticket->name = $request->name;
        $ticket->address = $request->address;
        $ticket->nearest_landmark = $request->nearest_landmark;
        $ticket->issue = $request->issue;
        $ticket->troubleshooting = $request->troubleshooting;
        $ticket->status = $request->status;

        // Save and trigger auditing
        $ticket->save();

        return back()->with('success', 'Tickets Updated Successfully!');

        // return response()->json($request);
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Tickets $tickets, $ticketsId)
    {
        return view('tickets.delete-tickets', [
            'item' => Tickets::where('id', $ticketsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tickets $tickets, $ticketsId)
    {

        /* Log ************************************************** */
        // $oldName = Tickets::where('id', $ticketsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Tickets "'.$oldName.'".']);
        /******************************************************** */

        // Tickets::where('id', $ticketsId)->delete();

        $ticket = Tickets::findOrFail($ticketsId);
        $ticket->delete();

        return redirect('/tickets');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Tickets::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Tickets "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Tickets::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Tickets::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Tickets "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Tickets::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Tickets::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Tickets "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Tickets::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
