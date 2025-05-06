<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Customers};
use App\Http\Requests\StoreCustomersRequest;
use App\Http\Requests\UpdateCustomersRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Smark\Smark\JSON;

class CustomersController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customers.customers', [
            'customers' => Customers::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('customers.trash-customers', [
            'customers' => Customers::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($customersId)
    {
        /* Log ************************************************** */
        $oldName = Customers::where('id', $customersId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Customers "'.$oldName.'".']);
        /******************************************************** */

        Customers::where('id', $customersId)->update(['isTrash' => '0']);

        return redirect('/customers');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create-customers');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomersRequest $request)
    {
        Customers::create(['customerId' => $request->customerId,'name' => $request->name,'description' => $request->description,'users_id' => $request->users_id]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Customers '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Customers Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customers $customers, $customersId)
    {
        return view('customers.show-customers', [
            'item' => Customers::where('customerId', $customersId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customers $customers, $customersId)
    {
        return view('customers.edit-customers', [
            'item' => Customers::where('id', $customersId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomersRequest $request, Customers $customers, $customersId)
    {
        /* Log ************************************************** */
        $oldName = Customers::where('id', $customersId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Customers from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Customers::where('id', $customersId)->update(['customerId' => $request->customerId,'name' => $request->name,'description' => $request->description,'users_id' => $request->users_id]);

        return back()->with('success', 'Customers Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Customers $customers, $customersId)
    {
        return view('customers.delete-customers', [
            'item' => Customers::where('id', $customersId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customers $customers, $customersId)
    {

        /* Log ************************************************** */
        $oldName = Customers::where('id', $customersId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Customers "'.$oldName.'".']);
        /******************************************************** */

        Customers::where('id', $customersId)->update(['isTrash' => '1']);

        return redirect('/customers');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Customers::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Customers "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Customers::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Customers::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Customers "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Customers::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Customers::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Customers "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Customers::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}