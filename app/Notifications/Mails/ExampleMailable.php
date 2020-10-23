<?php

namespace App\Notifications\Mails;

use Illuminate\Contracts\Mail\Mailable;

class ExampleMailable extends BaseMailable
{
    public function __construct()
    {
        parent::__construct();
    }

    public function build(): Mailable
    {
        $this->to('example@mail.com'); // please use a configuration
        $this->subject('Subject line'); // please use a translation

        return $this->view('mails.example');
    }
}
