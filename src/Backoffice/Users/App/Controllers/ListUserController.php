<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Controllers;

use Illuminate\Http\JsonResponse;
use Lightit\Backoffice\Users\App\Resources\UserResource;
use Lightit\Backoffice\Users\Domain\Actions\ListUserAction;

final readonly class ListUserController
{
    public function __invoke(
        ListUserAction $action,
    ): JsonResponse {
        $users = $action->execute();

        return UserResource::collection($users)
            ->response();
    }
}
