<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Topcpuusages};
use App\Http\Requests\StoreTopcpuusagesRequest;
use App\Http\Requests\UpdateTopcpuusagesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopcpuusagesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('topcpuusages.topcpuusages', [
            'topcpuusages' => Topcpuusages::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('topcpuusages.trash-topcpuusages', [
            'topcpuusages' => Topcpuusages::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($topcpuusagesId)
    {
        /* Log ************************************************** */
        $oldName = Topcpuusages::where('id', $topcpuusagesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Topcpuusages "'.$oldName.'".']);
        /******************************************************** */

        Topcpuusages::where('id', $topcpuusagesId)->update(['isTrash' => '0']);

        return redirect('/topcpuusages');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('topcpuusages.create-topcpuusages');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopcpuusagesRequest $request)
    {
        Topcpuusages::create(['name' => $request->name,'mac' => $request->mac,'cpuUtil' => $request->cpuUtil,'model' => $request->model,'modelVersion' => $request->modelVersion,'type' => $request->type]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Topcpuusages '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Topcpuusages Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Topcpuusages $topcpuusages, $topcpuusagesId)
    {
        return view('topcpuusages.show-topcpuusages', [
            'item' => Topcpuusages::where('id', $topcpuusagesId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topcpuusages $topcpuusages, $topcpuusagesId)
    {
        return view('topcpuusages.edit-topcpuusages', [
            'item' => Topcpuusages::where('id', $topcpuusagesId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopcpuusagesRequest $request, Topcpuusages $topcpuusages, $topcpuusagesId)
    {
        /* Log ************************************************** */
        $oldName = Topcpuusages::where('id', $topcpuusagesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Topcpuusages from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Topcpuusages::where('id', $topcpuusagesId)->update(['name' => $request->name,'mac' => $request->mac,'cpuUtil' => $request->cpuUtil,'model' => $request->model,'modelVersion' => $request->modelVersion,'type' => $request->type]);

        return back()->with('success', 'Topcpuusages Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Topcpuusages $topcpuusages, $topcpuusagesId)
    {
        return view('topcpuusages.delete-topcpuusages', [
            'item' => Topcpuusages::where('id', $topcpuusagesId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topcpuusages $topcpuusages, $topcpuusagesId)
    {

        /* Log ************************************************** */
        $oldName = Topcpuusages::where('id', $topcpuusagesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Topcpuusages "'.$oldName.'".']);
        /******************************************************** */

        Topcpuusages::where('id', $topcpuusagesId)->update(['isTrash' => '1']);

        return redirect('/topcpuusages');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Topcpuusages::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Topcpuusages "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Topcpuusages::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Topcpuusages::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Topcpuusages "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Topcpuusages::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Topcpuusages::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Topcpuusages "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Topcpuusages::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}