<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\SecurityContext;
use Illuminate\Console\Command;

class UserAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:users:add {username : The user\'s username.} {email : The user\'s email address.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a user to the backoffice.';

    /**
     * Execute the console command.
     *
     * @param SecurityContext $securityContext
     */
    public function handle(SecurityContext $securityContext)
    {
        $security = $securityContext->getSecurity('backoffice');

        $security->registerAndActivate([
            'username' => $this->argument('username'),
            'email' => $this->argument('email'),
            'password' => $this->secret('Insert password: '),
            'firstName' => $this->ask('Insert First Name: '),
            'lastName' => $this->ask('Insert Last Name: '),
        ]);

        $this->info('User created!');
    }
}
