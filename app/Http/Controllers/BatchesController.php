<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Batches};
use App\Http\Requests\StoreBatchesRequest;
use App\Http\Requests\UpdateBatchesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('batches.batches', [
            'batches' => Batches::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('batches.trash-batches', [
            'batches' => Batches::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($batchesId)
    {
        /* Log ************************************************** */
        $oldName = Batches::where('id', $batchesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Batches "'.$oldName.'".']);
        /******************************************************** */

        Batches::where('id', $batchesId)->update(['isTrash' => '0']);

        return redirect('/batches');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('batches.create-batches');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchesRequest $request)
    {
        Batches::create(['batch_number' => $request->batch_number]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Batches '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Batches Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Batches $batches, $batchesId)
    {
        return view('batches.show-batches', [
            'item' => Batches::where('id', $batchesId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batches $batches, $batchesId)
    {
        return view('batches.edit-batches', [
            'item' => Batches::where('id', $batchesId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBatchesRequest $request, Batches $batches, $batchesId)
    {
        /* Log ************************************************** */
        $oldName = Batches::where('id', $batchesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Batches from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Batches::where('id', $batchesId)->update(['batch_number' => $request->batch_number]);

        return back()->with('success', 'Batches Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Batches $batches, $batchesId)
    {
        return view('batches.delete-batches', [
            'item' => Batches::where('id', $batchesId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batches $batches, $batchesId)
    {

        /* Log ************************************************** */
        $oldName = Batches::where('id', $batchesId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Batches "'.$oldName.'".']);
        /******************************************************** */

        Batches::where('id', $batchesId)->update(['isTrash' => '1']);

        return redirect('/batches');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Batches::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Batches "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Batches::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Batches::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Batches "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Batches::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Batches::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Batches "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Batches::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}