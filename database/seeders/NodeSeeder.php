<?php

namespace Database\Seeders;

use App\Models\Node;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NodeSeeder extends Seeder
{
    public function run(): void
    {
        $mainBuilding = Zone::where('name', 'Main Building')->first();
        $scienceBuilding = Zone::where('name', 'Science Building')->first();
        $library = Zone::where('name', 'Library')->first();
        $cafeteria = Zone::where('name', 'Cafeteria')->first();

        Node::create([
            'name' => 'Node-MB-01',
            'location_x' => 9.6350,
            'location_y' => 124.8860,
            'zone_id' => $mainBuilding->id,
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);

        Node::create([
            'name' => 'Node-MB-02',
            'location_x' => 9.6355,
            'location_y' => 124.8865,
            'zone_id' => $mainBuilding->id,
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);

        Node::create([
            'name' => 'Node-SB-01',
            'location_x' => 9.6360,
            'location_y' => 124.8870,
            'zone_id' => $scienceBuilding->id,
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);

        Node::create([
            'name' => 'Node-LIB-01',
            'location_x' => 9.6345,
            'location_y' => 124.8855,
            'zone_id' => $library->id,
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);

        Node::create([
            'name' => 'Node-CAF-01',
            'location_x' => 9.6340,
            'location_y' => 124.8850,
            'zone_id' => $cafeteria->id,
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);
    }
}
