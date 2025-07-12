<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Clientdetails};
use App\Http\Requests\StoreClientdetailsRequest;
use App\Http\Requests\UpdateClientdetailsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientdetailsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientdetails.clientdetails', [
            'clientdetails' => Clientdetails::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('clientdetails.trash-clientdetails', [
            'clientdetails' => Clientdetails::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($clientdetailsId)
    {
        /* Log ************************************************** */
        $oldName = Clientdetails::where('id', $clientdetailsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Clientdetails "'.$oldName.'".']);
        /******************************************************** */

        Clientdetails::where('id', $clientdetailsId)->update(['isTrash' => '0']);

        return redirect('/clientdetails');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientdetails.create-clientdetails');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientdetailsRequest $request)
    {
        Clientdetails::create(['mac' => $request->mac,'name' => $request->name,'deviceType' => $request->deviceType,'switchName' => $request->switchName,'switchMac' => $request->switchMac,'port' => $request->port,'standardPort' => $request->standardPort,'trafficDown' => $request->trafficDown,'trafficUp' => $request->trafficUp,'uptime' => $request->uptime,'guest' => $request->guest,'blocked' => $request->blocked]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Clientdetails '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Clientdetails Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Clientdetails $clientdetails, $clientdetailsId)
    {
        return view('clientdetails.show-clientdetails', [
            'item' => Clientdetails::where('id', $clientdetailsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clientdetails $clientdetails, $clientdetailsId)
    {
        return view('clientdetails.edit-clientdetails', [
            'item' => Clientdetails::where('id', $clientdetailsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientdetailsRequest $request, Clientdetails $clientdetails, $clientdetailsId)
    {
        /* Log ************************************************** */
        $oldName = Clientdetails::where('id', $clientdetailsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Clientdetails from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Clientdetails::where('id', $clientdetailsId)->update(['mac' => $request->mac,'name' => $request->name,'deviceType' => $request->deviceType,'switchName' => $request->switchName,'switchMac' => $request->switchMac,'port' => $request->port,'standardPort' => $request->standardPort,'trafficDown' => $request->trafficDown,'trafficUp' => $request->trafficUp,'uptime' => $request->uptime,'guest' => $request->guest,'blocked' => $request->blocked]);

        return back()->with('success', 'Clientdetails Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Clientdetails $clientdetails, $clientdetailsId)
    {
        return view('clientdetails.delete-clientdetails', [
            'item' => Clientdetails::where('id', $clientdetailsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clientdetails $clientdetails, $clientdetailsId)
    {

        /* Log ************************************************** */
        $oldName = Clientdetails::where('id', $clientdetailsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Clientdetails "'.$oldName.'".']);
        /******************************************************** */

        Clientdetails::where('id', $clientdetailsId)->update(['isTrash' => '1']);

        return redirect('/clientdetails');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clientdetails::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Clientdetails "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Clientdetails::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clientdetails::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Clientdetails "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Clientdetails::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Clientdetails::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Clientdetails "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Clientdetails::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}