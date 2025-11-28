<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $shipment = null;
        $trackingNumber = $request->input('tracking');

        if ($trackingNumber) {
            $shipment = Shipment::where('tracking_number', $trackingNumber)->first();
        }

        return view('tracking', compact('shipment', 'trackingNumber'));
    }
}