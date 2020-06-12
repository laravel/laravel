<?php

namespace App\Console\Commands\Backoffice;

use Digbang\Backoffice\Repositories\DoctrineRoleRepository;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\Roles\Role;
use Digbang\Security\Security;
use Digbang\Security\SecurityContext;
use Digbang\Security\Users\User;
use Illuminate\Console\Command;

class AdminAddCommand extends Command
{
    private const SECURITY_CONTEXT = 'backoffice';
    private const ADMIN_ROLE_SLUG = 'admin';
    private const ADMIN_ROLE_NAME = 'Admin';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:admins:add {username : The user\'s username.} {email : The user\'s email address.} {context? : Security context of the user and role (default: backoffice).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an admin to the backoffice.';

    private ?Security $security = null;

    public function handle(SecurityContext $securityContext): void
    {
        try {
            $this->security = $securityContext->getSecurity($this->argument('context') ?? self::SECURITY_CONTEXT);

            $this->comment('Creating User...');
            $user = $this->createUser();

            $this->comment('Creating Role...');
            $role = $this->role();

            $this->comment('Assigning Permissions to Role...');
            $this->assignPermissions($role);

            $this->comment('Assigning Role to User...');
            $this->assignRole($user, $role);

            $this->info('Admin created!');
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }
    }

    private function createUser(): User
    {
        return $this->security->registerAndActivate([
            'username' => $this->argument('username'),
            'email' => $this->argument('email'),
            'password' => $this->secret('Password: '),
            'firstName' => $this->ask('First Name: '),
            'lastName' => $this->ask('Last Name: '),
        ]);
    }

    /**
     * @return Permissible|Role|null
     */
    private function role()
    {
        /** @var DoctrineRoleRepository $roleRepository */
        $roleRepository = $this->security->roles();

        $role = $roleRepository->findOneBy(['slug' => self::ADMIN_ROLE_SLUG]);

        if (! $role) {
            $role = $roleRepository->create(self::ADMIN_ROLE_NAME, self::ADMIN_ROLE_SLUG);
        }

        return $role;
    }

    /**
     * @param Role|Permissible $role
     */
    private function assignPermissions($role): void
    {
        /** @var DoctrineRoleRepository $roleRepository */
        $roleRepository = $this->security->roles();

        foreach ($this->security->permissions()->all() as $permission) {
            $role->addPermission($permission);
        }

        $roleRepository->save($role);
    }

    private function assignRole(User $user, Role $role): void
    {
        $this->security->users()->update($user, [
            'roles' => [$role->getRoleSlug()],
        ]);
    }
}
