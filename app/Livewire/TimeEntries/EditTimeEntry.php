<?php
namespace App\Livewire\TimeEntries;

use Livewire\Component;
use App\Models\TimeEntry;

class EditTimeEntry extends Component
{
    public $timeEntries;
    public $edit_id, $edit_project_id, $edit_hours, $edit_comment;
    public $editing = false;

    public function mount()
    {
        $this->timeEntries = TimeEntry::where('user_id', auth()->id())->latest()->get();
    }

    public function edit($id)
    {
        $entry = TimeEntry::findOrFail($id);
        $this->edit_id = $entry->id;
        $this->edit_project_id = $entry->project_id;
        $this->edit_hours = $entry->hours;
        $this->edit_comment = $entry->comment;
        $this->editing = true;
    }

    public function update()
    {
        $entry = TimeEntry::findOrFail($this->edit_id);
        $entry->update([
            'project_id' => $this->edit_project_id,
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
        return view('livewire.time-entries.edit-time-entry', [
            'timeEntries' => TimeEntry::where('user_id', auth()->id())->latest()->get(),
        ]);
    }
}

