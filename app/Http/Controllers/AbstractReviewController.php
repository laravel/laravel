<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AbstractUpload;

class AbstractReviewController extends Controller
{
    public function abstractReview()
    {
        // Check if the method is being called
        // dd('abstractReview method called');

        // Check if the user is authenticated
        if (auth()->check()) {
            // Fetch the super admin name here
            $superAdminName = auth()->user()->name;

            // Fetch all abstract uploads
            $abstractUploads = AbstractUpload::all();

            // Pass the data to the view
            return view('super_admin.abstractreview', compact('superAdminName', 'abstractUploads'));
        } else {
            // Redirect the user to the login page
            return redirect()->route('login');
        }
    }
}
