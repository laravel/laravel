<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\Roles\RoleRepository;
use Digbang\Security\SecurityContext;
use Illuminate\Console\Command;

class RoleAddCommand extends Command
{
    private const SECURITY_CONTEXT = 'backoffice';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:roles:add
        {name : The role name}
        {slug? : The role slug. If not provided, the role name will be slugified.}
        {context? : Security context of the user and role (default: backoffice).}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a role to the backoffice.';

    public function handle(SecurityContext $securityContext): void
    {
        $security = $securityContext->getSecurity($this->argument('context') ?? self::SECURITY_CONTEXT);

        /** @var RoleRepository $roles */
        $roles = $security->roles();

        $roles->create(
            $this->argument('name'),
            $this->argument('slug')
        );

        $this->info('Role created!');
    }
}
