<?php

namespace App\Domains\Hardware\Services;

use App\Models\Alert;
use App\Models\RfidLog;
use App\Models\SensorReading;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class HardwareService
{
    public function recordSensorReading(array $data): SensorReading
    {
        $reading = SensorReading::create([
            'node_id' => $data['node_id'],
            'sensor_type' => $data['sensor_type'],
            'value' => $data['value'],
            'unit' => $data['unit'],
            'timestamp' => now(),
        ]);

        Log::info('Sensor data received', $reading->toArray());

        return $reading;
    }

    public function recordBleScan(array $data): array
    {
        Log::info('BLE scan received', $data);

        return $data;
    }

    public function recordRfidScan(array $data): array|null
    {
        $student = Student::where('rfid_tag', $data['rfid_tag'])->first();

        if (!$student) {
            return null;
        }

        $log = RfidLog::create([
            'student_id' => $student->id,
            'rfid_tag' => $data['rfid_tag'],
            'action' => $data['action'],
            'location' => $data['location'],
            'timestamp' => now(),
        ]);

        Log::info('RFID scan logged', $log->toArray());

        return compact('student', 'log');
    }

    public function queueSmsAlert(array $data): Alert
    {
        Log::info('SMS placeholder - would send to ' . $data['recipient'], [
            'message' => $data['message'],
        ]);

        return Alert::create([
            'type' => 'occupancy',
            'recipient' => $data['recipient'],
            'message' => $data['message'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}