<?php

namespace App\Http\Controllers;

use App\Models\{Batches, Logs, Devices};
use App\Http\Requests\StoreDevicesRequest;
use App\Http\Requests\UpdateDevicesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevicesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $latestBatch = Batches::latest('id')->first();

        if (!$latestBatch) {
            return view('devices.devices', [
                'devices' => collect(), // return empty collection
            ]);
        }

        return view('devices.devices', [
            'devices' => Devices::where('isTrash', '0')
                                ->where('batch_number', $latestBatch->batch_number)
                                ->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('devices.trash-devices', [
            'devices' => Devices::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($devicesId)
    {
        /* Log ************************************************** */
        $oldName = Devices::where('id', $devicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Devices "'.$oldName.'".']);
        /******************************************************** */

        Devices::where('id', $devicesId)->update(['isTrash' => '0']);

        return redirect('/devices');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('devices.create-devices');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDevicesRequest $request)
    {
        Devices::create(['device_name' => $request->device_name,'ip_address' => $request->ip_address,'status' => $request->status,'model' => $request->model,'version' => $request->version,'uptime' => $request->uptime,'cpu' => $request->cpu,'memory' => $request->memory,'public_ip' => $request->public_ip,'link_speed' => $request->link_speed,'duplex' => $request->duplex,'siteId' => $request->siteId]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Devices '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Devices Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Devices $devices, $devicesId)
    {
        return view('devices.show-devices', [
            'item' => Devices::where('id', $devicesId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Devices $devices, $devicesId)
    {
        return view('devices.edit-devices', [
            'item' => Devices::where('id', $devicesId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDevicesRequest $request, Devices $devices, $devicesId)
    {
        /* Log ************************************************** */
        $oldName = Devices::where('id', $devicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Devices from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Devices::where('id', $devicesId)->update(['device_name' => $request->device_name,'ip_address' => $request->ip_address,'status' => $request->status,'model' => $request->model,'version' => $request->version,'uptime' => $request->uptime,'cpu' => $request->cpu,'memory' => $request->memory,'public_ip' => $request->public_ip,'link_speed' => $request->link_speed,'duplex' => $request->duplex,'siteId' => $request->siteId]);

        return back()->with('success', 'Devices Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Devices $devices, $devicesId)
    {
        return view('devices.delete-devices', [
            'item' => Devices::where('id', $devicesId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Devices $devices, $devicesId)
    {

        /* Log ************************************************** */
        $oldName = Devices::where('id', $devicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Devices "'.$oldName.'".']);
        /******************************************************** */

        Devices::where('id', $devicesId)->update(['isTrash' => '1']);

        return redirect('/devices');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Devices::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Devices "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Devices::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Devices::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Devices "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Devices::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Devices::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Devices "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Devices::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
