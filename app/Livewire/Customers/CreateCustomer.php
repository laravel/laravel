<?php
namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;

class CreateCustomer extends Component
{
    public $name, $email, $phone;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
    ];

    public function submit()
    {
        $this->validate();
        Customer::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Kunde oprettet.');
        $this->reset(['name', 'email', 'phone']);

        $this->dispatch('customerCreated');
    }

    public function render()
    {
        return view('livewire.customers.create-customer');
    }
}

