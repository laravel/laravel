<?php

declare(strict_types=1);

namespace Lightit\Users\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Users\App\Resources\UserResource;
use Lightit\Users\Domain\Models\User;

#[Group('Users')]
final readonly class GetUserController
{
    public function __invoke(User $user): JsonResponse
    {
        return UserResource::make($user)
            ->response();
    }
}
