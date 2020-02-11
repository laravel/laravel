<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\SecurityContext;
use Illuminate\Console\Command;

class RoleAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:roles:add {name : The role name} {slug? : The role slug. If not provided, the role name will be slugified }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a role to the backoffice.';

    /**
     * Execute the console command.
     */
    public function handle(SecurityContext $securityContext)
    {
        $security = $securityContext->getSecurity('backoffice');

        $security->roles()->create(
            $this->argument('name'),
            $this->argument('slug')
        );

        $this->info('Role created!');
    }
}
