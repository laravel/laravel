<?php

namespace App\Livewire\TimeEntries;

use App\Models\Project;
use App\Models\TimeEntry;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateTimeEntry extends Component
{
    public $projects;
    public $project_id;
    public $entry_date;
    public $start_time;
    public $end_time;
    public $hours;
    public $comment;

    protected $rules = [
        'project_id'  => 'required|exists:projects,id',
        'entry_date'  => 'required|date',
        'start_time'  => 'nullable|date_format:H:i',
        'end_time'    => 'nullable|date_format:H:i|after:start_time',
        'hours'       => 'required|numeric|min:0.25',
        'comment'     => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->projects = Project::all();
        $this->entry_date = now()->toDateString();
    }

    public function submit()
    {
        $data = $this->validate();

        Auth::user()->timeEntries()->create($data);

        session()->flash('success', 'Tidsregistreringen er gemt.');

        $this->reset(['project_id', 'start_time', 'end_time', 'hours', 'comment']);
    }

    public function render()
    {
        return view('livewire.time-entries.create-time-entry');
    }
}

