<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Controllers;

use Illuminate\Http\JsonResponse;
use Lightit\Backoffice\Users\App\Requests\UpsertUserRequest;
use Lightit\Backoffice\Users\App\Resources\UserResource;
use Lightit\Backoffice\Users\Domain\Actions\UpdateUserAction;
use Lightit\Backoffice\Users\Domain\Models\User;

final readonly class UpdateUserController
{
    public function __invoke(User $user, UpsertUserRequest $request, UpdateUserAction $updateUserAction): JsonResponse
    {
        $user = $updateUserAction->execute($user, $request->toDto());

        return UserResource::make($user)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
