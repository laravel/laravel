<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Controllers;

use Illuminate\Http\JsonResponse;
use Lightit\Backoffice\Users\App\Request\StoreUserRequest;
use Lightit\Backoffice\Users\App\Transformers\UserTransformer;
use Lightit\Backoffice\Users\Domain\Actions\StoreUserAction;

class StoreUserController
{
    public function __invoke(StoreUserRequest $request, StoreUserAction $storeUserAction): JsonResponse
    {
        $user = $storeUserAction->execute($request->toDto());

        return responder()
            ->success($user, UserTransformer::class)
            ->respond(JsonResponse::HTTP_CREATED);
    }
}
