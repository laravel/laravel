<?php

declare(strict_types=1);

namespace Lightit\Users\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Users\App\Requests\UpsertUserRequest;
use Lightit\Users\App\Resources\UserResource;
use Lightit\Users\Domain\Actions\StoreUserAction;

#[Group('Users')]
final readonly class StoreUserController
{
    public function __invoke(UpsertUserRequest $request, StoreUserAction $storeUserAction): JsonResponse
    {
        $user = $storeUserAction->execute($request->toDto());

        return UserResource::make($user)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
