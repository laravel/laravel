<?php

namespace App\Http\Controllers;

use App\Domains\Hardware\Services\HardwareService;
use App\Http\Requests\Api\V1\RfidScanRequest;
use App\Http\Requests\Api\V1\SensorDataRequest;
use Illuminate\Http\Request;

class HardwareController extends Controller
{
    public function __construct(private readonly HardwareService $hardwareService)
    {
    }

    // Placeholder function for receiving sensor data from ESP32
    public function receiveSensorData(SensorDataRequest $request)
    {
        $reading = $this->hardwareService->recordSensorReading($request->validated());

        return response()->json([
            'message' => 'Sensor data received successfully',
            'data' => $reading,
        ]);
    }

    // Placeholder function for receiving BLE scan data
    public function receiveBleScan(Request $request)
    {
        $data = $request->validate([
            'node_id' => 'required|exists:nodes,id',
            'device_address' => 'required|string',
            'rssi' => 'required|numeric',
        ]);

        $data = $this->hardwareService->recordBleScan($data);

        return response()->json([
            'message' => 'BLE scan data received successfully',
            'data' => $data,
        ]);
    }

    // Placeholder function for RFID scan
    public function scan(RfidScanRequest $request)
    {
        $scan = $this->hardwareService->recordRfidScan($request->validated());

        if (!$scan) {
            return response()->json([
                'message' => 'Student not found',
            ], 404);
        }

        return response()->json([
            'message' => 'RFID scan logged successfully',
            'student' => $scan['student'],
            'log' => $scan['log'],
        ]);
    }

    // Placeholder function for SMS sending
    public function sendSms(Request $request)
    {
        $data = $request->validate([
            'recipient' => 'required|string',
            'message' => 'required|string',
        ]);

        $this->hardwareService->queueSmsAlert($data);

        return response()->json([
            'message' => 'SMS placeholder - message logged',
        ]);
    }
}
