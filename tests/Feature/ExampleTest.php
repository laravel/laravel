<?php

namespace Tests\Feature;

use Digbang\Backoffice\Repositories\DoctrineRoleRepository;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\Roles\Role;
use Tests\Traits\RefreshDatabase;

uses(RefreshDatabase::class);

it('has welcome page')
    ->get('/backoffice')
    ->assertStatus(302);

it('Create Admin Role', function () {
    /** @var DoctrineRoleRepository $roleRepository */
    $roleRepository = security()->roles();

    $role = $roleRepository->create('Admin', 'admin');

    /** @var Role|Permissible $roleSearch */
    $roleSearch = $roleRepository->findOneBy(['slug' => 'admin']);

    assertEquals($role->getRoleId(), $roleSearch->getRoleId());
});
