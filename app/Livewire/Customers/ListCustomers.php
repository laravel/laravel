<?php
namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\On;

class ListCustomers extends Component
{
    public $editing = false;
    public $edit_id, $edit_name, $edit_email, $edit_phone;

    #[On('customerCreated')]
    public function render()
    {
        return view('livewire.customers.list-customers', [
            'customers' => Customer::latest()->get(),
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->edit_id = $customer->id;
        $this->edit_name = $customer->name;
        $this->edit_email = $customer->email;
        $this->edit_phone = $customer->phone;
        $this->editing = true;
    }

    public function update()
    {
        $customer = Customer::findOrFail($this->edit_id);
        $customer->update([
            'name' => $this->edit_name,
            'email' => $this->edit_email,
            'phone' => $this->edit_phone,
        ]);

        session()->flash('success', 'Kunde opdateret.');
        $this->editing = false;
    }

    public function delete($id)
    {
        Customer::findOrFail($id)->delete();
        session()->flash('success', 'Kunde slettet.');
    }
}

