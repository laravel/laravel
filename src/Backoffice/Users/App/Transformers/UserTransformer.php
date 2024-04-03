<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Transformers;

use Flugg\Responder\Transformers\Transformer;
use Lightit\Backoffice\Users\Domain\Models\User;

class UserTransformer extends Transformer
{
    public function transform(User $user): array
    {
        return [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email
        ];
    }
}
