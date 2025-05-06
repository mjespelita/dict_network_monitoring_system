<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Auditlogs};
use App\Http\Requests\StoreAuditlogsRequest;
use App\Http\Requests\UpdateAuditlogsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditlogsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auditlogs.auditlogs', [
            'auditlogs' => Auditlogs::where('isTrash', '0')->paginate(15)
        ]);
    }

    public function trash()
    {
        return view('auditlogs.trash-auditlogs', [
            'auditlogs' => Auditlogs::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($auditlogsId)
    {
        /* Log ************************************************** */
        $oldName = Auditlogs::where('id', $auditlogsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Auditlogs "'.$oldName.'".']);
        /******************************************************** */

        Auditlogs::where('id', $auditlogsId)->update(['isTrash' => '0']);

        return redirect('/auditlogs');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auditlogs.create-auditlogs');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuditlogsRequest $request)
    {
        Auditlogs::create(['time' => $request->time,'operator' => $request->operator,'resource' => $request->resource,'ip' => $request->ip,'auditType' => $request->auditType,'level' => $request->level,'result' => $request->result,'content' => $request->content,'label' => $request->label,'oldValue' => $request->oldValue,'newValue' => $request->newValue]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Auditlogs '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Auditlogs Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Auditlogs $auditlogs, $auditlogsId)
    {
        return view('auditlogs.show-auditlogs', [
            'item' => Auditlogs::where('id', $auditlogsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auditlogs $auditlogs, $auditlogsId)
    {
        return view('auditlogs.edit-auditlogs', [
            'item' => Auditlogs::where('id', $auditlogsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuditlogsRequest $request, Auditlogs $auditlogs, $auditlogsId)
    {
        /* Log ************************************************** */
        $oldName = Auditlogs::where('id', $auditlogsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Auditlogs from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Auditlogs::where('id', $auditlogsId)->update(['time' => $request->time,'operator' => $request->operator,'resource' => $request->resource,'ip' => $request->ip,'auditType' => $request->auditType,'level' => $request->level,'result' => $request->result,'content' => $request->content,'label' => $request->label,'oldValue' => $request->oldValue,'newValue' => $request->newValue]);

        return back()->with('success', 'Auditlogs Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Auditlogs $auditlogs, $auditlogsId)
    {
        return view('auditlogs.delete-auditlogs', [
            'item' => Auditlogs::where('id', $auditlogsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auditlogs $auditlogs, $auditlogsId)
    {

        /* Log ************************************************** */
        $oldName = Auditlogs::where('id', $auditlogsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Auditlogs "'.$oldName.'".']);
        /******************************************************** */

        Auditlogs::where('id', $auditlogsId)->update(['isTrash' => '1']);

        return redirect('/auditlogs');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Auditlogs::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Auditlogs "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Auditlogs::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Auditlogs::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Auditlogs "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Auditlogs::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Auditlogs::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Auditlogs "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Auditlogs::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}