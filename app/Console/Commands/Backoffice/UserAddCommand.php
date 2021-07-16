<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\SecurityContext;
use Illuminate\Console\Command;

class UserAddCommand extends Command
{
    private const SECURITY_CONTEXT = 'backoffice';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:users:add
        {username : The user\'s username.}
        {email : The user\'s email address.}
        {context? : Security context of the user and role (default: backoffice).}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a user to the backoffice.';

    public function handle(SecurityContext $securityContext): void
    {
        $security = $securityContext->getSecurity($this->argument('context') ?? self::SECURITY_CONTEXT);

        $security->registerAndActivate([
            'username' => $this->argument('username'),
            'email' => $this->argument('email'),
            'password' => $this->secret('Insert password: '),
            'firstName' => $this->ask('Insert First Name: '),
            'lastName' => $this->ask('Insert Last Name: '),
            'forcePasswordChange' => $this->confirm('Force password change?', false),
        ]);

        $this->info('User created!');
    }
}
