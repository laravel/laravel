<?php

namespace App\Http\Controllers;

use App\Domains\Disaster\Services\DisasterEventService;
use App\Http\Requests\Api\V1\StoreDisasterEventRequest;
use App\Http\Requests\Api\V1\UpdateDisasterEventRequest;
use App\Models\DisasterEvent;
use Illuminate\Http\Request;

class DisasterEventController extends Controller
{
    public function __construct(private readonly DisasterEventService $disasterEventService)
    {
    }

    public function index()
    {
        return response()->json(DisasterEvent::with('node')->get());
    }

    public function store(StoreDisasterEventRequest $request)
    {
        $event = $this->disasterEventService->create($request->validated());

        return response()->json($event, 201);
    }

    public function show(DisasterEvent $disasterEvent)
    {
        return response()->json($disasterEvent->load('node'));
    }

    public function update(UpdateDisasterEventRequest $request, DisasterEvent $disasterEvent)
    {
        $disasterEvent = $this->disasterEventService->update($disasterEvent, $request->validated());

        return response()->json($disasterEvent);
    }

    public function destroy(DisasterEvent $disasterEvent)
    {
        $disasterEvent->delete();

        return response()->json(['message' => 'Disaster event deleted successfully']);
    }
}
