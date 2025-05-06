<?php

namespace App\Http\Controllers;

use App\Models\{Customers, Logs, Sites, Uploads};
use App\Http\Requests\StoreUploadsRequest;
use App\Http\Requests\UpdateUploadsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Smark\Smark\File;
use Smark\Smark\JSON;

class UploadsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('uploads.uploads', [
            'uploads' => Uploads::where('isTrash', '0')->orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('uploads.trash-uploads', [
            'uploads' => Uploads::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($uploadsId)
    {
        /* Log ************************************************** */
        $oldName = Uploads::where('id', $uploadsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Uploads "'.$oldName.'".']);
        /******************************************************** */

        Uploads::where('id', $uploadsId)->update(['isTrash' => '0']);

        return redirect('/uploads');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('uploads.create-uploads');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUploadsRequest $request)
    {
        // initiate new folder name

        $newFolder = uniqid('folder', true);

        // file upload process

        // CUSTOMERS -----------------------------

        File::upload($request->file('customers'), 'uploads/'.$newFolder.'/customers');

        $customerFilename = File::$filename;

        $newUploadedJsonFile = JSON::jsonRead('storage/uploads/'.$newFolder.'/customers/'.$customerFilename);

        Customers::whereNot('id', '')->delete();

        foreach ($newUploadedJsonFile['result']['data'] as $key => $value) {
            Customers::create([
                'customerId' => isset($value['customerId']) ? $value['customerId'] : null,
                'name' => isset($value['customerName']) ? $value['customerName'] : null,
                'description' => isset($value['description']) ? $value['description'] : null, // Check if description exists
                'users_id' => Auth::user()->id,
            ]);
        }

        // END CUSTOMERS -----------------------------

        // SITES -----------------------------

        File::upload($request->file('sites'), 'uploads/'.$newFolder.'/sites');

        $siteFilename = File::$filename;

        $newUploadedJsonFile = JSON::jsonRead('storage/uploads/'.$newFolder.'/sites/'.$siteFilename);

        Sites::whereNot('id', '')->delete();

        foreach ($newUploadedJsonFile['result']['data'] as $key => $value) {
            Sites::create([
                'name' => isset($value['siteName']) ? $value['siteName'] : null,
                'siteId' => isset($value['siteId']) ? $value['siteId'] : null,
                'customerId' => isset($value['customerId']) ? $value['customerId'] : null,
                'customerName' => isset($value['customerName']) ? $value['customerName'] : null,
                'region' => isset($value['region']) ? $value['region'] : null,
                'timezone' => isset($value['timeZone']) ? $value['timeZone'] : null,
                'scenario' => isset($value['scenario']) ? $value['scenario'] : null,
                'wan' => isset($value['wan']) ? $value['wan'] : null,
                'connectedApNum' => isset($value['connectedApNum']) ? $value['connectedApNum'] : null,
                'disconnectedApNum' => isset($value['disconnectedApNum']) ? $value['disconnectedApNum'] : null,
                'isolatedApNum' => isset($value['isolatedApNum']) ? $value['isolatedApNum'] : null,
                'connectedSwitchNum' => isset($value['connectedSwitchNum']) ? $value['connectedSwitchNum'] : null,
                'disconnectedSwitchNum' => isset($value['disconnectedSwitchNum']) ? $value['disconnectedSwitchNum'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
            ]);
        }

        // END CUSTOMERS -----------------------------

        // input data to database

        Uploads::create(['folder' => $newFolder, 'users_id' => Auth::user()->id]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Uploads '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Uploads Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Uploads $uploads, $uploadsId)
    {
        return view('uploads.show-uploads', [
            'item' => Uploads::where('id', $uploadsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Uploads $uploads, $uploadsId)
    {
        return view('uploads.edit-uploads', [
            'item' => Uploads::where('id', $uploadsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUploadsRequest $request, Uploads $uploads, $uploadsId)
    {
        /* Log ************************************************** */
        $oldName = Uploads::where('id', $uploadsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Uploads from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        // get the existing folder of the customer

        $folder = Uploads::where('id', $uploadsId)->value('folder');

        // CUSTOMERS -----------------------------

        // Scan the folder and get all files and subfolders (excluding "." and "..")

        $files = array_diff(scandir('storage/uploads/'.$folder.'/customers'), array('.', '..'));

        foreach ($files as $file) {
            $filePath = 'storage/uploads/'.$folder.'/customers' . DIRECTORY_SEPARATOR . $file;
            
            // Check if it's a file (not a directory)
            if (is_file($filePath)) {
                unlink($filePath); // Delete the file
            }
        }

        File::upload($request->file('customers'), 'uploads/'.$folder.'/customers');

        $customerFilename = File::$filename;

        $newUploadedJsonFile = JSON::jsonRead('storage/uploads/'.$folder.'/customers/'.$customerFilename);

        Customers::whereNot('id', '')->delete();

        foreach ($newUploadedJsonFile['result']['data'] as $key => $value) {
            Customers::create([
                'customerId' => $value['customerId'],
                'name' => $value['customerName'],
                'description' => isset($value['description']) ? $value['description'] : null, // Check if description exists
                'users_id' => Auth::user()->id,
            ]);
        }

        // END CUSTOMERS -----------------------------

        // SITES -----------------------------

        $files = array_diff(scandir('storage/uploads/'.$folder.'/sites'), array('.', '..'));

        foreach ($files as $file) {
            $filePath = 'storage/uploads/'.$folder.'/sites' . DIRECTORY_SEPARATOR . $file;
            
            // Check if it's a file (not a directory)
            if (is_file($filePath)) {
                unlink($filePath); // Delete the file
            }
        }

        File::upload($request->file('sites'), 'uploads/'.$folder.'/sites');

        $sitesFilename = File::$filename;

        $newUploadedJsonFile = JSON::jsonRead('storage/uploads/'.$folder.'/sites/'.$sitesFilename);

        Sites::whereNot('id', '')->delete();

        foreach ($newUploadedJsonFile['result']['data'] as $key => $value) {
            Sites::create([
                'name' => isset($value['siteName']) ? $value['siteName'] : null,
                'siteId' => isset($value['siteId']) ? $value['siteId'] : null,
                'customerId' => isset($value['customerId']) ? $value['customerId'] : null,
                'customerName' => isset($value['customerName']) ? $value['customerName'] : null,
                'region' => isset($value['region']) ? $value['region'] : null,
                'timezone' => isset($value['timeZone']) ? $value['timeZone'] : null,
                'scenario' => isset($value['scenario']) ? $value['scenario'] : null,
                'wan' => isset($value['wan']) ? $value['wan'] : null,
                'connectedApNum' => isset($value['connectedApNum']) ? $value['connectedApNum'] : null,
                'disconnectedApNum' => isset($value['disconnectedApNum']) ? $value['disconnectedApNum'] : null,
                'isolatedApNum' => isset($value['isolatedApNum']) ? $value['isolatedApNum'] : null,
                'connectedSwitchNum' => isset($value['connectedSwitchNum']) ? $value['connectedSwitchNum'] : null,
                'disconnectedSwitchNum' => isset($value['disconnectedSwitchNum']) ? $value['disconnectedSwitchNum'] : null,
                'type' => isset($value['type']) ? $value['type'] : null,
            ]);
        }

        // END SITES -----------------------------

        // update the uploads data - update method

        Uploads::where('id', $uploadsId)->update(['folder' => $folder,'users_id' => Auth::user()->id]);

        return back()->with('success', 'Uploads Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Uploads $uploads, $uploadsId)
    {
        return view('uploads.delete-uploads', [
            'item' => Uploads::where('id', $uploadsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Uploads $uploads, $uploadsId)
    {

        /* Log ************************************************** */
        $oldName = Uploads::where('id', $uploadsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Uploads "'.$oldName.'".']);
        /******************************************************** */

        Uploads::where('id', $uploadsId)->update(['isTrash' => '1']);

        return redirect('/uploads');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Uploads::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Uploads "'.$oldName.'".']);
            /******************************************************** */

            // delete all customers

            Customers::whereNot('id', '')->delete();

            // delete all sites

            Sites::whereNot('id', '')->delete();

            $folder = Uploads::where('id', $value)->value('folder');

            $files = array_diff(scandir('storage/uploads/'.$folder), array('.', '..'));

            foreach ($files as $file) {
                $filePath = 'storage/uploads/'.$folder . DIRECTORY_SEPARATOR . $file;  // Correctly form the file path
            
                // If it's a file, delete it
                if (is_file($filePath)) {
                    unlink($filePath);  // Delete the file
                } 
                // If it's a directory, delete all contents inside the directory
                elseif (is_dir($filePath)) {
                    // Scan the subdirectory for files and delete them
                    $subFiles = array_diff(scandir($filePath), array('.', '..'));
            
                    foreach ($subFiles as $subFile) {
                        $subFilePath = $filePath . DIRECTORY_SEPARATOR . $subFile;
                        if (is_file($subFilePath)) {
                            unlink($subFilePath);  // Delete file inside subdirectory
                        }
                    }
                    rmdir($filePath);  // Remove the now-empty subdirectory
                }
            }

            rmdir('storage/uploads/'.$folder);

            $deletable = Uploads::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Uploads::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Uploads "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Uploads::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Uploads::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Uploads "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Uploads::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}