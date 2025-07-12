<?php

namespace App\Http\Controllers;

use App\Models\{Batches, Logs, Clientstats};
use App\Http\Requests\StoreClientstatsRequest;
use App\Http\Requests\UpdateClientstatsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientstatsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $latestBatch = Batches::latest('id')->first();

        if (!$latestBatch) {
            return view('clientstats.clientstats', [
                'clientstats' => collect(), // Return empty if no batch exists
            ]);
        }

        return view('clientstats.clientstats', [
            'clientstats' => Clientstats::where('isTrash', '0')
                                        ->where('batch_number', $latestBatch->batch_number)
                                        ->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('clientstats.trash-clientstats', [
            'clientstats' => Clientstats::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($clientstatsId)
    {
        /* Log ************************************************** */
        $oldName = Clientstats::where('id', $clientstatsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Clientstats "'.$oldName.'".']);
        /******************************************************** */

        Clientstats::where('id', $clientstatsId)->update(['isTrash' => '0']);

        return redirect('/clientstats');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientstats.create-clientstats');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientstatsRequest $request)
    {
        Clientstats::create(['total' => $request->total,'wireless' => $request->wireless,'wired' => $request->wired,'num2g' => $request->num2g,'num5g' => $request->num5g,'num6g' => $request->num6g,'numUser' => $request->numUser,'numGuest' => $request->numGuest,'numWirelessUser' => $request->numWirelessUser,'numWirelessGuest' => $request->numWirelessGuest,'num2gUser' => $request->num2gUser,'num5gUser' => $request->num5gUser,'num6gUser' => $request->num6gUser,'num2gGuest' => $request->num2gGuest,'num5gGuest' => $request->num5gGuest,'num6gGuest' => $request->num6gGuest,'poor' => $request->poor,'fair' => $request->fair,'noData' => $request->noData,'good' => $request->good,'siteId' => $request->siteId]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Clientstats '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Clientstats Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Clientstats $clientstats, $clientstatsId)
    {
        return view('clientstats.show-clientstats', [
            'item' => Clientstats::where('id', $clientstatsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clientstats $clientstats, $clientstatsId)
    {
        return view('clientstats.edit-clientstats', [
            'item' => Clientstats::where('id', $clientstatsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientstatsRequest $request, Clientstats $clientstats, $clientstatsId)
    {
        /* Log ************************************************** */
        $oldName = Clientstats::where('id', $clientstatsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Clientstats from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Clientstats::where('id', $clientstatsId)->update(['total' => $request->total,'wireless' => $request->wireless,'wired' => $request->wired,'num2g' => $request->num2g,'num5g' => $request->num5g,'num6g' => $request->num6g,'numUser' => $request->numUser,'numGuest' => $request->numGuest,'numWirelessUser' => $request->numWirelessUser,'numWirelessGuest' => $request->numWirelessGuest,'num2gUser' => $request->num2gUser,'num5gUser' => $request->num5gUser,'num6gUser' => $request->num6gUser,'num2gGuest' => $request->num2gGuest,'num5gGuest' => $request->num5gGuest,'num6gGuest' => $request->num6gGuest,'poor' => $request->poor,'fair' => $request->fair,'noData' => $request->noData,'good' => $request->good,'siteId' => $request->siteId]);

        return back()->with('success', 'Clientstats Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Clientstats $clientstats, $clientstatsId)
    {
        return view('clientstats.delete-clientstats', [
            'item' => Clientstats::where('id', $clientstatsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clientstats $clientstats, $clientstatsId)
    {

        /* Log ************************************************** */
        $oldName = Clientstats::where('id', $clientstatsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Clientstats "'.$oldName.'".']);
        /******************************************************** */

        Clientstats::where('id', $clientstatsId)->update(['isTrash' => '1']);

        return redirect('/clientstats');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clientstats::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Clientstats "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Clientstats::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clientstats::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Clientstats "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Clientstats::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clientstats::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Clientstats "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Clientstats::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
