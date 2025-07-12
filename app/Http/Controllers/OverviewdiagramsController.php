<?php

namespace App\Http\Controllers;

use App\Models\{Batches, Logs, Overviewdiagrams};
use App\Http\Requests\StoreOverviewdiagramsRequest;
use App\Http\Requests\UpdateOverviewdiagramsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OverviewdiagramsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $latestBatch = Batches::latest('id')->first();

        if (!$latestBatch) {
            return view('overviewdiagrams.overviewdiagrams', [
                'overviewdiagrams' => collect(), // Return empty if no batch
            ]);
        }

        return view('overviewdiagrams.overviewdiagrams', [
            'overviewdiagrams' => Overviewdiagrams::where('isTrash', '0')
                                                ->where('batch_number', $latestBatch->batch_number)
                                                ->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('overviewdiagrams.trash-overviewdiagrams', [
            'overviewdiagrams' => Overviewdiagrams::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($overviewdiagramsId)
    {
        /* Log ************************************************** */
        $oldName = Overviewdiagrams::where('id', $overviewdiagramsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Overviewdiagrams "'.$oldName.'".']);
        /******************************************************** */

        Overviewdiagrams::where('id', $overviewdiagramsId)->update(['isTrash' => '0']);

        return redirect('/overviewdiagrams');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('overviewdiagrams.create-overviewdiagrams');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOverviewdiagramsRequest $request)
    {
        Overviewdiagrams::create(['totalGatewayNum' => $request->totalGatewayNum,'connectedGatewayNum' => $request->connectedGatewayNum,'disconnectedGatewayNum' => $request->disconnectedGatewayNum,'totalSwitchNum' => $request->totalSwitchNum,'connectedSwitchNum' => $request->connectedSwitchNum,'disconnectedSwitchNum' => $request->disconnectedSwitchNum,'totalPorts' => $request->totalPorts,'availablePorts' => $request->availablePorts,'powerConsumption' => $request->powerConsumption,'totalApNum' => $request->totalApNum,'connectedApNum' => $request->connectedApNum,'isolatedApNum' => $request->isolatedApNum,'disconnectedApNum' => $request->disconnectedApNum,'totalClientNum' => $request->totalClientNum,'wiredClientNum' => $request->wiredClientNum,'wirelessClientNum' => $request->wirelessClientNum,'guestNum' => $request->guestNum]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Overviewdiagrams '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Overviewdiagrams Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Overviewdiagrams $overviewdiagrams, $overviewdiagramsId)
    {
        return view('overviewdiagrams.show-overviewdiagrams', [
            'item' => Overviewdiagrams::where('id', $overviewdiagramsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Overviewdiagrams $overviewdiagrams, $overviewdiagramsId)
    {
        return view('overviewdiagrams.edit-overviewdiagrams', [
            'item' => Overviewdiagrams::where('id', $overviewdiagramsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOverviewdiagramsRequest $request, Overviewdiagrams $overviewdiagrams, $overviewdiagramsId)
    {
        /* Log ************************************************** */
        $oldName = Overviewdiagrams::where('id', $overviewdiagramsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Overviewdiagrams from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Overviewdiagrams::where('id', $overviewdiagramsId)->update(['totalGatewayNum' => $request->totalGatewayNum,'connectedGatewayNum' => $request->connectedGatewayNum,'disconnectedGatewayNum' => $request->disconnectedGatewayNum,'totalSwitchNum' => $request->totalSwitchNum,'connectedSwitchNum' => $request->connectedSwitchNum,'disconnectedSwitchNum' => $request->disconnectedSwitchNum,'totalPorts' => $request->totalPorts,'availablePorts' => $request->availablePorts,'powerConsumption' => $request->powerConsumption,'totalApNum' => $request->totalApNum,'connectedApNum' => $request->connectedApNum,'isolatedApNum' => $request->isolatedApNum,'disconnectedApNum' => $request->disconnectedApNum,'totalClientNum' => $request->totalClientNum,'wiredClientNum' => $request->wiredClientNum,'wirelessClientNum' => $request->wirelessClientNum,'guestNum' => $request->guestNum]);

        return back()->with('success', 'Overviewdiagrams Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Overviewdiagrams $overviewdiagrams, $overviewdiagramsId)
    {
        return view('overviewdiagrams.delete-overviewdiagrams', [
            'item' => Overviewdiagrams::where('id', $overviewdiagramsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Overviewdiagrams $overviewdiagrams, $overviewdiagramsId)
    {

        /* Log ************************************************** */
        $oldName = Overviewdiagrams::where('id', $overviewdiagramsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Overviewdiagrams "'.$oldName.'".']);
        /******************************************************** */

        Overviewdiagrams::where('id', $overviewdiagramsId)->update(['isTrash' => '1']);

        return redirect('/overviewdiagrams');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Overviewdiagrams::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Overviewdiagrams "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Overviewdiagrams::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Overviewdiagrams::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Overviewdiagrams "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Overviewdiagrams::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Overviewdiagrams::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Overviewdiagrams "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Overviewdiagrams::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
