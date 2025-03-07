<?php
namespace App\Livewire\TimeEntries;

use Livewire\Component;
use App\Models\TimeEntry;
use App\Models\Project;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ListAndEditTimeEntries extends Component
{
    use WithPagination;

    public $edit_id, $edit_project_id, $edit_entry_date, $edit_hours, $edit_comment;
    public $editing = false;
    public $projects;

    public function mount()
    {
        $this->projects = Project::where('active', true)->get();
    }

    public function edit($id)
    {
        $entry = TimeEntry::findOrFail($id);
        $this->edit_id = $entry->id;
        $this->edit_project_id = $entry->project_id;
        $this->edit_entry_date = $entry->entry_date;
        $this->edit_hours = $entry->hours;
        $this->edit_comment = $entry->comment;
        $this->editing = true;
    }

    public function update()
    {
        $this->validate([
            'edit_project_id' => 'required|exists:projects,id',
            'edit_entry_date' => 'required|date',
            'edit_hours' => 'required|numeric|min:0.25',
            'edit_comment' => 'nullable|string|max:255',
        ]);

        $entry = TimeEntry::findOrFail($this->edit_id);
        $entry->update([
            'project_id' => $this->edit_project_id,
            'entry_date' => $this->edit_entry_date,
            'hours' => $this->edit_hours,
            'comment' => $this->edit_comment,
        ]);

        session()->flash('success', 'Tidsregistrering opdateret.');
        $this->editing = false;
    }

    public function delete($id)
    {
        TimeEntry::findOrFail($id)->delete();
        session()->flash('success', 'Tidsregistrering slettet.');
    }

public function render()
{
    return view('livewire.time-entries.list-and-edit-time-entries', [
        'timeEntries' => TimeEntry::with(['user', 'project.customer'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10),
    ]);
}

    
}

