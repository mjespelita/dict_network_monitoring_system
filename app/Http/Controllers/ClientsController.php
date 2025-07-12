<?php

namespace App\Http\Controllers;

use App\Models\{Batches, Logs, Clients};
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $latestBatch = Batches::latest('id')->first();

        if (!$latestBatch) {
            return view('clients.clients', [
                'clients' => collect(), // Empty collection if no batch yet
            ]);
        }

        return view('clients.clients', [
            'clients' => Clients::where('isTrash', '0')
                                ->where('batch_number', $latestBatch->batch_number)
                                ->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('clients.trash-clients', [
            'clients' => Clients::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($clientsId)
    {
        /* Log ************************************************** */
        $oldName = Clients::where('id', $clientsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Clients "'.$oldName.'".']);
        /******************************************************** */

        Clients::where('id', $clientsId)->update(['isTrash' => '0']);

        return redirect('/clients');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create-clients');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientsRequest $request)
    {
        Clients::create(['mac_address' => $request->mac_address,'device_name' => $request->device_name,'device_type' => $request->device_type,'connected_device_type' => $request->connected_device_type,'switch_name' => $request->switch_name,'port' => $request->port,'standard_port' => $request->standard_port,'network_theme' => $request->network_theme,'uptime' => $request->uptime,'traffic_down' => $request->traffic_down,'traffic_up' => $request->traffic_up,'status' => $request->status,'siteId' => $request->siteId]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Clients '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Clients Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Clients $clients, $clientsId)
    {
        return view('clients.show-clients', [
            'item' => Clients::where('id', $clientsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clients $clients, $clientsId)
    {
        return view('clients.edit-clients', [
            'item' => Clients::where('id', $clientsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientsRequest $request, Clients $clients, $clientsId)
    {
        /* Log ************************************************** */
        $oldName = Clients::where('id', $clientsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Clients from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Clients::where('id', $clientsId)->update(['mac_address' => $request->mac_address,'device_name' => $request->device_name,'device_type' => $request->device_type,'connected_device_type' => $request->connected_device_type,'switch_name' => $request->switch_name,'port' => $request->port,'standard_port' => $request->standard_port,'network_theme' => $request->network_theme,'uptime' => $request->uptime,'traffic_down' => $request->traffic_down,'traffic_up' => $request->traffic_up,'status' => $request->status,'siteId' => $request->siteId]);

        return back()->with('success', 'Clients Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Clients $clients, $clientsId)
    {
        return view('clients.delete-clients', [
            'item' => Clients::where('id', $clientsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clients $clients, $clientsId)
    {

        /* Log ************************************************** */
        $oldName = Clients::where('id', $clientsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Clients "'.$oldName.'".']);
        /******************************************************** */

        Clients::where('id', $clientsId)->update(['isTrash' => '1']);

        return redirect('/clients');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clients::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Clients "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Clients::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clients::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Clients "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Clients::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clients::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Clients "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Clients::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
