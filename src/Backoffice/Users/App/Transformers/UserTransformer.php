<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Transformers;

use Flugg\Responder\Transformers\Transformer;
use Lightit\Backoffice\Users\Domain\Models\User;

class UserTransformer extends Transformer
{
    /**
     * @return array{id: int, name: string, email: string}
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
