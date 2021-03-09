<?php

namespace ProjectName\Services;

use ProjectName\Entities\User;
use ProjectName\Payloads\UserPayload;
use ProjectName\Payloads\UserUpdatePayload;
use ProjectName\Repositories\PersistRepository;

class Users
{
    private PersistRepository $persistRepository;

    public function __construct(PersistRepository $persistRepository)
    {
        $this->persistRepository = $persistRepository;
    }

    public function create(UserPayload $payload): User
    {
        $user = new User($payload);
        $this->persistRepository->save($user);

        return $user;
    }

    public function update(User $user, UserUpdatePayload $payload): User
    {
        $user->update($payload);
        $this->persistRepository->save($user);

        return $user;
    }
}
