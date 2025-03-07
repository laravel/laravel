<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Customer;
use Livewire\Attributes\On;

class ListProjects extends Component
{
    public $editing = false;
    public $edit_id, $edit_customer_id, $edit_name, $edit_description, $edit_budget_hours, $edit_active;
    public $customers;

    public function mount()
    {
        $this->customers = Customer::all(); // Sikrer at $customers altid er sat
    }

    #[On('projectCreated')]
    public function render()
    {
        return view('livewire.projects.list-projects', [
            'projects' => Project::with('customer')->latest()->get(),
            'customers' => $this->customers,
        ]);
    }

public function edit($id)
{
    $project = Project::findOrFail($id);
    $this->edit_id = $project->id;
    $this->edit_customer_id = $project->customer_id;
    $this->edit_name = $project->name;
    $this->edit_description = $project->description;
    $this->edit_budget_hours = $project->budget_hours;
    $this->edit_active = $project->active ? true : false; // Sikrer, at værdien altid er boolean
    $this->editing = true;
}

public function update()
{
    logger("Checkbox værdi før update:", ['edit_active' => $this->edit_active]);

    $project = Project::findOrFail($this->edit_id);

    $project->update([
        'customer_id' => $this->edit_customer_id,
        'name' => $this->edit_name,
        'description' => $this->edit_description,
        'budget_hours' => $this->edit_budget_hours,
        'active' => $this->edit_active ? 1 : 0, // Konverterer false korrekt til 0
    ]);

    logger("Checkbox værdi efter update:", ['active' => $project->active]);

    session()->flash('success', 'Projekt opdateret.');
    $this->editing = false;
}


public function toggleActive($id)
{
    $project = Project::findOrFail($id);
    $project->update(['active' => !$project->active]);

    session()->flash('success', 'Projektstatus opdateret.');
}

public function delete($id)
{
    $project = Project::with('timeEntries')->findOrFail($id);

    if ($project->timeEntries()->count() > 0) {
        session()->flash('error', 'Projektet har registrerede timer og kan ikke slettes. Sæt det som inaktivt i stedet.');
        return;
    }

    $project->delete();
    session()->flash('success', 'Projekt slettet.');
}
}
