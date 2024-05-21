<?php

declare(strict_types=1);

namespace Lightit\Users\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Users\App\Resources\UserResource;
use Lightit\Users\Domain\Actions\ListUserAction;

#[Group('Users')]
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
