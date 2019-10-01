<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Permissions\Permissible;
use Doctrine\ORM\EntityNotFoundException;

class RolePermissionAddCommand extends AddPermissionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:roles:permissions:add {role : The role slug} {permissions? : A comma-separated list of permissions. If omitted, all permissions will be added.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission/s to a backoffice role.';

    /**
     * {@inheritdoc}
     */
    protected function getPermissible(SecurityApi $security)
    {
        $slug = $this->argument('role');
        $role = $security->roles()->findOneBy(['slug' => $slug]);

        $this->assertRole($role, "Role [$slug] does not exist.");

        return $role;
    }

    /**
     * @param object|null $role
     * @param string $message
     *
     * @throws \OutOfBoundsException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    private function assertRole($role, $message)
    {
        if (! $role) {
            throw new EntityNotFoundException($message, 1);
        }

        if (! $role instanceof Permissible) {
            throw new \OutOfBoundsException('The configured Role class needs to extend ' . Permissible::class . ' to use permissions.', 2);
        }
    }
}
