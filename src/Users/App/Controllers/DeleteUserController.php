<?php

declare(strict_types=1);

namespace Lightit\Users\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Users\Domain\Models\User;

#[Group('Users')]
final readonly class DeleteUserController
{
    public function __invoke(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
