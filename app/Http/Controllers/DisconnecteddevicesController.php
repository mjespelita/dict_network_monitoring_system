<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Disconnecteddevices};
use App\Http\Requests\StoreDisconnecteddevicesRequest;
use App\Http\Requests\UpdateDisconnecteddevicesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisconnecteddevicesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('disconnecteddevices.disconnecteddevices', [
            'disconnecteddevices' => Disconnecteddevices::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('disconnecteddevices.trash-disconnecteddevices', [
            'disconnecteddevices' => Disconnecteddevices::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($disconnecteddevicesId)
    {
        /* Log ************************************************** */
        $oldName = Disconnecteddevices::where('id', $disconnecteddevicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Disconnecteddevices "'.$oldName.'".']);
        /******************************************************** */

        Disconnecteddevices::where('id', $disconnecteddevicesId)->update(['isTrash' => '0']);

        return redirect('/disconnecteddevices');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('disconnecteddevices.create-disconnecteddevices');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDisconnecteddevicesRequest $request)
    {
        Disconnecteddevices::create(['name' => $request->name,'device_name' => $request->device_name,'device_mac' => $request->device_mac,'device_type' => $request->device_type,'status' => $request->status]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Disconnecteddevices '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Disconnecteddevices Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disconnecteddevices $disconnecteddevices, $disconnecteddevicesId)
    {
        return view('disconnecteddevices.show-disconnecteddevices', [
            'item' => Disconnecteddevices::where('id', $disconnecteddevicesId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disconnecteddevices $disconnecteddevices, $disconnecteddevicesId)
    {
        return view('disconnecteddevices.edit-disconnecteddevices', [
            'item' => Disconnecteddevices::where('id', $disconnecteddevicesId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDisconnecteddevicesRequest $request, Disconnecteddevices $disconnecteddevices, $disconnecteddevicesId)
    {
        /* Log ************************************************** */
        $oldName = Disconnecteddevices::where('id', $disconnecteddevicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Disconnecteddevices from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Disconnecteddevices::where('id', $disconnecteddevicesId)->update(['name' => $request->name,'device_name' => $request->device_name,'device_mac' => $request->device_mac,'device_type' => $request->device_type,'status' => $request->status]);

        return back()->with('success', 'Disconnecteddevices Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Disconnecteddevices $disconnecteddevices, $disconnecteddevicesId)
    {
        return view('disconnecteddevices.delete-disconnecteddevices', [
            'item' => Disconnecteddevices::where('id', $disconnecteddevicesId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disconnecteddevices $disconnecteddevices, $disconnecteddevicesId)
    {

        /* Log ************************************************** */
        $oldName = Disconnecteddevices::where('id', $disconnecteddevicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Disconnecteddevices "'.$oldName.'".']);
        /******************************************************** */

        Disconnecteddevices::where('id', $disconnecteddevicesId)->update(['isTrash' => '1']);

        return redirect('/disconnecteddevices');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Disconnecteddevices::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Disconnecteddevices "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Disconnecteddevices::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Disconnecteddevices::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Disconnecteddevices "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Disconnecteddevices::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Disconnecteddevices::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Disconnecteddevices "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Disconnecteddevices::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}