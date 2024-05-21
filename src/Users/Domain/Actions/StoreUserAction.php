<?php

declare(strict_types=1);

namespace Lightit\Users\Domain\Actions;

use Lightit\Users\App\Notifications\UserRegisteredNotification;
use Lightit\Users\Domain\DataTransferObjects\UserDto;
use Lightit\Users\Domain\Models\User;

class StoreUserAction
{
    public function execute(UserDto $userDto): User
    {
        $user = new User();

        $user->name = $userDto->name;
        $user->email = $userDto->emailAddress;
        $user->password = $userDto->password;

        $user->saveOrFail();

        $user->notify(new UserRegisteredNotification());

        return $user;
    }
}
