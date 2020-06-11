<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\SecurityContext;
use Digbang\Security\Users\User;
use Digbang\Security\Users\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Console\Command;

class UserRoleAddCommand extends Command
{
    private const SECURITY_CONTEXT = 'backoffice';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:users:roles:add
        {username : The user\'s username}
        {role : The role slug}
        {context? : Security context of the user and role (default: backoffice).}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a role to a backoffice user.';

    public function handle(SecurityContext $securityContext, EntityManager $entityManager): int
    {
        $security = $securityContext->getSecurity($this->argument('context') ?? self::SECURITY_CONTEXT);

        $username = $this->argument('username');
        $roleName = $this->argument('role');

        /** @var UserRepository $users */
        $users = $security->users();

        /** @var User|Roleable $user */
        $user = $users->findOneBy(['username' => $username]);

        try {
            $this->assertUser($user, "Username [$username] does not exist.");

            /** @var Role $role */
            $role = $security->roles()->findBySlug($roleName);

            $this->assertRoleExists($role, "Role [$roleName] does not exist. You must use the role slug to identify it.");

            $user->addRole($role);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->info("Role [$roleName] added to user [$username].");
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return $e->getCode() !== 0 ? $e->getCode() : 255;
        }

        return 0;
    }

    /**
     * @throws \OutOfBoundsException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function assertUser(?User $user, ?string $message = null): void
    {
        if (! $user) {
            throw new EntityNotFoundException($message ?? 'User does not exist.', 1);
        }

        if (! $user instanceof Roleable) {
            throw new \OutOfBoundsException('The configured User class needs to extend ' . Roleable::class . ' to use roles.', 2);
        }
    }

    /**
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function assertRoleExists(?Role $role, string $message): void
    {
        if (! $role) {
            throw new EntityNotFoundException($message, 3);
        }
    }
}
