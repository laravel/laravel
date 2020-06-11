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
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'firstName' => $user->getName()->getFirstName(),
            'lastName' => $user->getName()->getLastName(),
        ];
    }
}
