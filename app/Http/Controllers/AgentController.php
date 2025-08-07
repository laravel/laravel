<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AgentController extends Controller
{
    public function index(): View
    {
        $agents = Agent::latest()->paginate(20);
        return view('admin.agents.index', compact('agents'));
    }

    public function create(): View
    {
        return view('admin.agents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:agents,slug',
            'model' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'prompt' => 'nullable|string',
            'avatar_url' => 'nullable|url',
            'welcome_message' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'config' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        Agent::create($data);

        return redirect()->route('admin.agents.index')->with('status', 'Agent created');
    }

    public function edit(Agent $agent): View
    {
        return view('admin.agents.edit', compact('agent'));
    }

    public function update(Request $request, Agent $agent): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:agents,slug,'.$agent->id,
            'model' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'prompt' => 'nullable|string',
            'avatar_url' => 'nullable|url',
            'welcome_message' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'config' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        $agent->update($data);

        return redirect()->route('admin.agents.index')->with('status', 'Agent updated');
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        $agent->delete();
        return redirect()->route('admin.agents.index')->with('status', 'Agent deleted');
    }

    public function showPublic(string $slug): View
    {
        $agent = Agent::where('slug', $slug)->where('is_public', true)->firstOrFail();
        return view('agents.public', compact('agent'));
    }

    public function embed(string $slug): View
    {
        $agent = Agent::where('slug', $slug)->where('is_public', true)->firstOrFail();
        return view('agents.embed', compact('agent'));
    }
}
