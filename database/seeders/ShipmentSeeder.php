<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shipment;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shipment::create([
            'tracking_number' => 'FC123456789',
            'status' => 'in_transit',
            'origin' => 'New York, USA',
            'destination' => 'London, UK',
            'description' => 'Electronics shipment',
            'weight' => 5.5,
        ]);

        Shipment::create([
            'tracking_number' => 'FC987654321',
            'status' => 'delivered',
            'origin' => 'Tokyo, Japan',
            'destination' => 'Sydney, Australia',
            'description' => 'Machinery parts',
            'weight' => 25.0,
        ]);

        Shipment::create([
            'tracking_number' => 'FC555666777',
            'status' => 'pending',
            'origin' => 'Mumbai, India',
            'destination' => 'Dubai, UAE',
            'description' => 'Textiles',
            'weight' => 10.0,
        ]);
    }
}