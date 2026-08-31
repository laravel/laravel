<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\V1\StoreNodeRequest;
use App\Http\Requests\Api\V1\UpdateNodeRequest;
use App\Models\Node;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    public function index()
    {
        return response()->json(Node::with('zone')->get());
    }

    public function store(StoreNodeRequest $request)
    {
        $node = Node::create($request->validated());

        return response()->json($node, 201);
    }

    public function show(Node $node)
    {
        return response()->json($node->load('zone', 'sensorReadings'));
    }

    public function update(UpdateNodeRequest $request, Node $node)
    {
        $node->update($request->validated());

        return response()->json($node);
    }

    public function destroy(Node $node)
    {
        $node->delete();

        return response()->json(['message' => 'Node deleted successfully']);
    }
}
