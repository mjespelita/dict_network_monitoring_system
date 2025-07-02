<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Restorations};
use App\Http\Requests\StoreRestorationsRequest;
use App\Http\Requests\UpdateRestorationsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestorationsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('restorations.restorations', [
            'restorations' => Restorations::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('restorations.trash-restorations', [
            'restorations' => Restorations::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($restorationsId)
    {
        /* Log ************************************************** */
        $oldName = Restorations::where('id', $restorationsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Restorations "'.$oldName.'".']);
        /******************************************************** */

        Restorations::where('id', $restorationsId)->update(['isTrash' => '0']);

        return redirect('/restorations');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('restorations.create-restorations');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestorationsRequest $request)
    {
        Restorations::create([
            'name' => $request->name,
            'siteId' => $request->siteId,
            'time' => $request->time,
            'troubleshoot' => $request->troubleshoot,
            'reason' => $request->reason
        ]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Restorations '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Restorations Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restorations $restorations, $restorationsId)
    {
        return view('restorations.show-restorations', [
            'item' => Restorations::where('id', $restorationsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restorations $restorations, $restorationsId)
    {
        return view('restorations.edit-restorations', [
            'item' => Restorations::where('id', $restorationsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestorationsRequest $request, Restorations $restorations, $restorationsId)
    {
        /* Log ************************************************** */
        $oldName = Restorations::where('id', $restorationsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Restorations from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Restorations::where('id', $restorationsId)->update(['name' => $request->name,'siteId' => $request->siteId,'time' => $request->time,'troubleshoot' => $request->troubleshoot]);

        return back()->with('success', 'Restorations Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Restorations $restorations, $restorationsId)
    {
        return view('restorations.delete-restorations', [
            'item' => Restorations::where('id', $restorationsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restorations $restorations, $restorationsId)
    {

        /* Log ************************************************** */
        $oldName = Restorations::where('id', $restorationsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Restorations "'.$oldName.'".']);
        /******************************************************** */

        Restorations::where('id', $restorationsId)->update(['isTrash' => '1']);

        return redirect('/restorations');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Restorations::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Restorations "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Restorations::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Restorations::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Restorations "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Restorations::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Restorations::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Restorations "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Restorations::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
