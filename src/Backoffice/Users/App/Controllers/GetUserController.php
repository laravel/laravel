<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Controllers;

use Illuminate\Http\JsonResponse;
use Lightit\Backoffice\Users\App\Resources\UserResource;
use Lightit\Backoffice\Users\Domain\Models\User;

final readonly class GetUserController
{
    public function __invoke(User $user): JsonResponse
    {
        return UserResource::make($user)
            ->response();
    }
}
