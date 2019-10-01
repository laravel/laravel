<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\SecurityContext;
use Doctrine\ORM\EntityManager;
use Illuminate\Console\Command;

abstract class AddPermissionCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @param SecurityContext $securityContext
     * @param EntityManager   $entityManager
     *
     * @return int
     */
    public function handle(SecurityContext $securityContext, EntityManager $entityManager)
    {
        try {
            $security = $securityContext->getSecurity('backoffice');

            $permissible = $this->getPermissible($security);
            $permissions = $this->getPermissions($security);

            foreach ($permissions as $permission) {
                $permissible->addPermission($permission);

                $this->info("Permission [$permission] added.");
            }

            $entityManager->flush($permissible);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $e->getCode() !== 0 ? $e->getCode() : 1;
        }

        return 0;
    }

    /**
     * @param SecurityApi $security
     *
     * @return string[]
     */
    protected function getPermissions(SecurityApi $security)
    {
        $permissions = $this->argument('permissions');

        if ($permissions) {
            return array_map('trim', explode(',', $permissions));
        }

        return $security->permissions()->all();
    }

    /**
     * @param SecurityApi $security
     *
     * @return Permissible
     */
    abstract protected function getPermissible(SecurityApi $security);
}
