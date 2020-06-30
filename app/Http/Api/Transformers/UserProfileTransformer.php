<?php

namespace App\Http\Api\Transformers;

use Flugg\Responder\Transformers\Transformer;
use ProjectName\Entities\User;

class UserProfileTransformer extends Transformer
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getName()->firstName(),
            'lastName' => $user->getName()->lastName(),
        ];
    }
}
