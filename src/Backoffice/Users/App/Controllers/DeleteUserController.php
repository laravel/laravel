<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Controllers;

use Illuminate\Http\JsonResponse;
use Lightit\Backoffice\Users\Domain\Models\User;

class DeleteUserController
{
    public function __invoke(User $user): JsonResponse
    {
        $user->delete();

        return responder()
            ->success()
            ->respond();
    }
}
