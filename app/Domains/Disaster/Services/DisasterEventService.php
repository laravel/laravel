<?php

namespace App\Domains\Disaster\Services;

use App\Models\DisasterEvent;
use App\Models\SystemState;
use Illuminate\Support\Facades\DB;

class DisasterEventService
{
    public function create(array $data): DisasterEvent
    {
        return DB::transaction(function () use ($data) {
            $event = DisasterEvent::create([
                'type' => $data['type'],
                'severity' => $data['severity'],
                'node_id' => $data['node_id'],
                'location' => $data['location'],
                'status' => 'active',
                'started_at' => now(),
            ]);

            SystemState::first()?->update([
                'disaster_mode' => true,
                'check_occupancy' => true,
                'current_disaster_id' => $event->id,
            ]);

            return $event;
        });
    }

    public function update(DisasterEvent $event, array $data): DisasterEvent
    {
        return DB::transaction(function () use ($event, $data) {
            $event->update($data);

            if (($data['status'] ?? null) === 'resolved') {
                $event->update(['resolved_at' => now()]);
                SystemState::first()?->update([
                    'disaster_mode' => false,
                    'check_occupancy' => false,
                    'current_disaster_id' => null,
                ]);
            }

            return $event;
        });
    }
}