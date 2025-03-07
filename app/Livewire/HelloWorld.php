<?php

namespace App\Livewire;

use Livewire\Component;

class HelloWorld extends Component
{
    public $message = "Velkommen til din tidsregistreringsapp!";

    public function render()
    {
        return view('livewire.hello-world');
    }
}

