<?php

declare(strict_types=1);

namespace Lightit\Users\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Users\App\Requests\UpsertUserRequest;
use Lightit\Users\App\Resources\UserResource;
use Lightit\Users\Domain\Actions\UpdateUserAction;
use Lightit\Users\Domain\Models\User;

#[Group('Users')]
final readonly class UpdateUserController
{
    public function __invoke(User $user, UpsertUserRequest $request, UpdateUserAction $updateUserAction): JsonResponse
    {
        $user = $updateUserAction->execute($user, $request->toDto());

        return UserResource::make($user)
            ->response();
    }
}
