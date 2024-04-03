<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\Domain\Actions;

use Illuminate\Contracts\Hashing\Hasher;
use Lightit\Backoffice\Users\Domain\DataTransferObjects\UserDto;
use Lightit\Backoffice\Users\Domain\Models\User;

class StoreUserAction
{
    public function __construct(private readonly Hasher $hasher)
    {
    }

    public function execute(UserDto $userDto): User
    {
        return User::create([
            'name' => $userDto->getName(),
            'email' => $userDto->getEmail(),
            'password' => $this->hasher->make($userDto->getPassword()),
        ]);
    }
}
