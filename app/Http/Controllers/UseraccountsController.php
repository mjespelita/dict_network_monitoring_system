<?php

namespace App\Http\Controllers;

use App\Models\{Logs, User, Useraccounts};
use App\Http\Requests\StoreUseraccountsRequest;
use App\Http\Requests\UpdateUseraccountsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UseraccountsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('useraccounts.useraccounts', [
            'useraccounts' => User::orderBy('id', 'desc')->paginate(50)
        ]);
    }

    public function trash()
    {
        return view('useraccounts.trash-useraccounts', [
            'useraccounts' => Useraccounts::where('isTrash', '1')->paginate(50)
        ]);
    }

    public function restore($useraccountsId)
    {
        /* Log ************************************************** */
        $oldName = Useraccounts::where('id', $useraccountsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Useraccounts "'.$oldName.'".']);
        /******************************************************** */

        Useraccounts::where('id', $useraccountsId)->update(['isTrash' => '0']);

        return redirect('/useraccounts');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('useraccounts.create-useraccounts');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUseraccountsRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Useraccounts '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Useraccounts Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Useraccounts $useraccounts, $useraccountsId)
    {
        return view('useraccounts.show-useraccounts', [
            'item' => User::where('id', $useraccountsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Useraccounts $useraccounts, $useraccountsId)
    {
        return view('useraccounts.edit-useraccounts', [
            'item' => Useraccounts::where('id', $useraccountsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUseraccountsRequest $request, Useraccounts $useraccounts, $useraccountsId)
    {
        /* Log ************************************************** */
        $oldName = Useraccounts::where('id', $useraccountsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Useraccounts from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Useraccounts::where('id', $useraccountsId)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return back()->with('success', 'Useraccounts Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Useraccounts $useraccounts, $useraccountsId)
    {
        return view('useraccounts.delete-useraccounts', [
            'item' => Useraccounts::where('id', $useraccountsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Useraccounts $useraccounts, $useraccountsId)
    {
        /******************************************************** */

        Useraccounts::where('id', $useraccountsId)->delete();

        return redirect('/useraccounts');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Useraccounts::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Useraccounts "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Useraccounts::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Useraccounts::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Useraccounts "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Useraccounts::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Useraccounts::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Useraccounts "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Useraccounts::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}
