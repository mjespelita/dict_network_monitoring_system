<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Restoreddevices};
use App\Http\Requests\StoreRestoreddevicesRequest;
use App\Http\Requests\UpdateRestoreddevicesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestoreddevicesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('restoreddevices.restoreddevices', [
            'restoreddevices' => Restoreddevices::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('restoreddevices.trash-restoreddevices', [
            'restoreddevices' => Restoreddevices::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($restoreddevicesId)
    {
        /* Log ************************************************** */
        $oldName = Restoreddevices::where('id', $restoreddevicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Restoreddevices "'.$oldName.'".']);
        /******************************************************** */

        Restoreddevices::where('id', $restoreddevicesId)->update(['isTrash' => '0']);

        return redirect('/restoreddevices');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('restoreddevices.create-restoreddevices');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestoreddevicesRequest $request)
    {
        Restoreddevices::create(['name' => $request->name,'device_name' => $request->device_name,'device_mac' => $request->device_mac,'device_type' => $request->device_type,'status' => $request->status,'ticket_number' => $request->ticket_number,'reason' => $request->reason,'troubleshoot' => $request->troubleshoot]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Restoreddevices '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Restoreddevices Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restoreddevices $restoreddevices, $restoreddevicesId)
    {
        return view('restoreddevices.show-restoreddevices', [
            'item' => Restoreddevices::where('id', $restoreddevicesId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restoreddevices $restoreddevices, $restoreddevicesId)
    {
        return view('restoreddevices.edit-restoreddevices', [
            'item' => Restoreddevices::where('id', $restoreddevicesId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestoreddevicesRequest $request, Restoreddevices $restoreddevices, $restoreddevicesId)
    {
        /* Log ************************************************** */
        $oldName = Restoreddevices::where('id', $restoreddevicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Restoreddevices from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Restoreddevices::where('id', $restoreddevicesId)->update(['name' => $request->name,'device_name' => $request->device_name,'device_mac' => $request->device_mac,'device_type' => $request->device_type,'status' => $request->status,'ticket_number' => $request->ticket_number,'reason' => $request->reason,'troubleshoot' => $request->troubleshoot]);

        return back()->with('success', 'Restoreddevices Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Restoreddevices $restoreddevices, $restoreddevicesId)
    {
        return view('restoreddevices.delete-restoreddevices', [
            'item' => Restoreddevices::where('id', $restoreddevicesId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restoreddevices $restoreddevices, $restoreddevicesId)
    {

        /* Log ************************************************** */
        $oldName = Restoreddevices::where('id', $restoreddevicesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Restoreddevices "'.$oldName.'".']);
        /******************************************************** */

        Restoreddevices::where('id', $restoreddevicesId)->update(['isTrash' => '1']);

        return redirect('/restoreddevices');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Restoreddevices::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Restoreddevices "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Restoreddevices::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Restoreddevices::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Restoreddevices "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Restoreddevices::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Restoreddevices::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Restoreddevices "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Restoreddevices::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}