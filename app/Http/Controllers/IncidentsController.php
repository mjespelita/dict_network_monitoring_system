<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Incidents};
use App\Http\Requests\StoreIncidentsRequest;
use App\Http\Requests\UpdateIncidentsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('incidents.incidents', [
            'incidents' => Incidents::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('incidents.trash-incidents', [
            'incidents' => Incidents::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($incidentsId)
    {
        /* Log ************************************************** */
        $oldName = Incidents::where('id', $incidentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Incidents "'.$oldName.'".']);
        /******************************************************** */

        Incidents::where('id', $incidentsId)->update(['isTrash' => '0']);

        return redirect('/incidents');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('incidents.create-incidents');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncidentsRequest $request)
    {
        Incidents::create(['name' => $request->name,'siteId' => $request->siteId,'time' => $request->time]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Incidents '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Incidents Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Incidents $incidents, $incidentsId)
    {
        return view('incidents.show-incidents', [
            'item' => Incidents::where('id', $incidentsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incidents $incidents, $incidentsId)
    {
        return view('incidents.edit-incidents', [
            'item' => Incidents::where('id', $incidentsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentsRequest $request, Incidents $incidents, $incidentsId)
    {
        /* Log ************************************************** */
        $oldName = Incidents::where('id', $incidentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Incidents from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Incidents::where('id', $incidentsId)->update(['name' => $request->name,'siteId' => $request->siteId,'time' => $request->time]);

        return back()->with('success', 'Incidents Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Incidents $incidents, $incidentsId)
    {
        return view('incidents.delete-incidents', [
            'item' => Incidents::where('id', $incidentsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incidents $incidents, $incidentsId)
    {

        /* Log ************************************************** */
        $oldName = Incidents::where('id', $incidentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Incidents "'.$oldName.'".']);
        /******************************************************** */

        Incidents::where('id', $incidentsId)->update(['isTrash' => '1']);

        return redirect('/incidents');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Incidents::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Incidents "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Incidents::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Incidents::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Incidents "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Incidents::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Incidents::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Incidents "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Incidents::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}