<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lightit\Backoffice\Users\App\Transformers\UserTransformer;
use Lightit\Backoffice\Users\Domain\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class ListUserController
{
    public function __invoke(Request $request): JsonResponse
    {
        $users = QueryBuilder::for(User::class)
            ->get();

        return responder()
            ->success($users, UserTransformer::class)
            ->respond();
    }
}
