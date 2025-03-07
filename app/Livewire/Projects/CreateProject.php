<?php
namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Customer;

class CreateProject extends Component
{
    public $customers;
    public $customer_id, $name, $description, $budget_hours;

    protected $rules = [
        'customer_id'  => 'required|exists:customers,id',
        'name'         => 'required|string|max:255',
        'description'  => 'nullable|string',
        'budget_hours' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->customers = Customer::all();
    }

public function submit()
{
    $this->validate();

    Project::create([
        'customer_id'  => $this->customer_id,
        'name'         => $this->name,
        'description'  => $this->description,
        'budget_hours' => $this->budget_hours,
        'active'       => true, // Aktivt som standard
    ]);

    session()->flash('success', 'Projekt oprettet.');
    $this->reset(['customer_id', 'name', 'description', 'budget_hours']);

    $this->dispatch('projectCreated');
}


    public function render()
    {
        return view('livewire.projects.create-project');
    }
}

