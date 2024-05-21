<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\Domain\Actions;

use Lightit\Backoffice\Users\Domain\DataTransferObjects\UserDto;
use Lightit\Backoffice\Users\Domain\Models\User;

class UpdateUserAction
{
    public function execute(User $user, UserDto $userDto): User
    {
        $user->name = $userDto->name;
        $user->email = $userDto->emailAddress;
        $user->password = $userDto->password;

        $user->save();

        return $user;
    }
}
