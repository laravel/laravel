<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\AbstractUpload; 
use Illuminate\Support\Facades\Storage;

class AbstractUploadController extends Controller
{
    public function abstractForm()
    {
        // Retrieve abstract data associated with the current user
        $user = Auth::user();
        $abstracts = $user->abstracts; // Assuming there's a relationship defined between User and Abstract models

        // Retrieve profile data for the current user
        $profile = $user;

        return view('admin.abstractupload', compact('abstracts', 'profile'));
    }

    public function create()
    {
        return view('abstract_upload.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'theme' => 'required',
            'file' => 'required|mimes:pdf|max:2048', // Assuming you're allowing only PDF files with a maximum size of 2MB
        ]);

        $user = Auth::user();

        // Check if the user has already uploaded two abstracts
        if ($user->abstractUploads()->count() >= 2) {
            return redirect()->back()->with('error', 'You are allowed to upload only two abstracts. Please delete one if you want to upload more.');
        }

        $theme = $request->input('theme');
        $file = $request->file('file');

        // Generate unique abstract upload ID
        $abstractUploadId = 'IIMATM2024_' . $theme . '_' . AbstractUpload::where('theme', $theme)->count() + 1;

        // Rename and move uploaded file to storage
        $filePath = $file->storeAs('abstracts', $abstractUploadId . '.' . $file->getClientOriginalExtension());

        // Create abstract upload record
        $abstractUpload = new AbstractUpload();
        $abstractUpload->theme = $theme;
        $abstractUpload->file_path = $filePath;
        $abstractUpload->user_id = $user->id;
        $abstractUpload->abstract_upload_id = $abstractUploadId;
        $abstractUpload->name = $user->name; // Fetching from the user table
        $abstractUpload->organization_name = $user->organization_name; // Fetching from the user table
        $abstractUpload->save();

        return redirect()->back()->with('success', 'Abstract uploaded successfully.');
    }
    
    public function destroy($id)
    {
        // Find the abstract upload by its ID
        $abstractUpload = AbstractUpload::findOrFail($id);

        // Ensure that the authenticated user owns the abstract upload
        if ($abstractUpload->user_id !== auth()->id()) {
            return back()->with('error', 'You are not authorized to delete this abstract upload.');
        }

        // Delete the associated PDF file
        Storage::delete($abstractUpload->file_path);

        // Delete the abstract upload
        $abstractUpload->delete();

        return redirect()->route('admin.abstractupload')->with('success', 'Abstract upload deleted successfully.');
    }
}
