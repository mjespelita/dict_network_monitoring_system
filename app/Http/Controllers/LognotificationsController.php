<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Lognotifications};
use App\Http\Requests\StoreLognotificationsRequest;
use App\Http\Requests\UpdateLognotificationsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LognotificationsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('lognotifications.lognotifications', [
            'lognotifications' => Lognotifications::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('lognotifications.trash-lognotifications', [
            'lognotifications' => Lognotifications::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($lognotificationsId)
    {
        /* Log ************************************************** */
        $oldName = Lognotifications::where('id', $lognotificationsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Lognotifications "'.$oldName.'".']);
        /******************************************************** */

        Lognotifications::where('id', $lognotificationsId)->update(['isTrash' => '0']);

        return redirect('/lognotifications');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lognotifications.create-lognotifications');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLognotificationsRequest $request)
    {
        Lognotifications::create(['key' => $request->key,'shortMsg' => $request->shortMsg,'siteId' => $request->siteId]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Lognotifications '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Lognotifications Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lognotifications $lognotifications, $lognotificationsId)
    {
        return view('lognotifications.show-lognotifications', [
            'item' => Lognotifications::where('id', $lognotificationsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lognotifications $lognotifications, $lognotificationsId)
    {
        return view('lognotifications.edit-lognotifications', [
            'item' => Lognotifications::where('id', $lognotificationsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLognotificationsRequest $request, Lognotifications $lognotifications, $lognotificationsId)
    {
        /* Log ************************************************** */
        $oldName = Lognotifications::where('id', $lognotificationsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Lognotifications from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Lognotifications::where('id', $lognotificationsId)->update(['key' => $request->key,'shortMsg' => $request->shortMsg,'siteId' => $request->siteId]);

        return back()->with('success', 'Lognotifications Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Lognotifications $lognotifications, $lognotificationsId)
    {
        return view('lognotifications.delete-lognotifications', [
            'item' => Lognotifications::where('id', $lognotificationsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lognotifications $lognotifications, $lognotificationsId)
    {

        /* Log ************************************************** */
        $oldName = Lognotifications::where('id', $lognotificationsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Lognotifications "'.$oldName.'".']);
        /******************************************************** */

        Lognotifications::where('id', $lognotificationsId)->update(['isTrash' => '1']);

        return redirect('/lognotifications');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Lognotifications::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Lognotifications "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Lognotifications::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Lognotifications::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Lognotifications "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Lognotifications::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Lognotifications::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Lognotifications "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Lognotifications::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}