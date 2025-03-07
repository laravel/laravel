<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TimeEntry;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $totalHours, $recentEntries, $activeProjects;

    public function mount()
    {
        $this->totalHours = TimeEntry::where('user_id', Auth::id())->sum('hours');
        $this->recentEntries = TimeEntry::where('user_id', Auth::id())->latest()->take(5)->get();
        $this->activeProjects = Project::where('active', true)->count();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

