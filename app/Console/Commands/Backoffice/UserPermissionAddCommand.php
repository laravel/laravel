<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Permissions\Permissible;
use Doctrine\ORM\EntityNotFoundException;

class UserPermissionAddCommand extends AddPermissionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:users:permissions:add
        {username : The username of the user}
        {permissions? : A comma-separated list of permissions. If omitted, all permissions will be added.}
        {context? : Security context of the user and role (default: backoffice).}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission/s to a backoffice user.';

    protected function getPermissible(SecurityApi $security): Permissible
    {
        $username = $this->argument('username');

        /** @var Permissible $user */
        $user = $security->users()->findOneBy(['username' => $username]);

        $this->assertUser($user, "Username [$username] does not exist.");

        return $user;
    }

    /**
     * @throws \OutOfBoundsException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function assertUser(?object $user, string $message): void
    {
        if (! $user) {
            throw new EntityNotFoundException($message, 1);
        }

        if (! $user instanceof Permissible) {
            throw new \OutOfBoundsException('The configured User class needs to extend ' . Permissible::class . ' to use permissions.', 2);
        }
    }
}
