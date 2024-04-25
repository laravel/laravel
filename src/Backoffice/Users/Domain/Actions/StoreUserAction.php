<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\Domain\Actions;

use Lightit\Backoffice\Users\App\Notifications\UserRegistered;
use Lightit\Backoffice\Users\Domain\DataTransferObjects\UserDto;
use Lightit\Backoffice\Users\Domain\Models\User;

class StoreUserAction
{
    public function execute(UserDto $userDto): User
    {
        $user = new User([
            'name' => $userDto->getName(),
            'email' => $userDto->getEmail(),
            'password' => $userDto->getPassword(),
        ]);

        $user->save();

        $user->notify(new UserRegistered());

        return $user;
    }
}
