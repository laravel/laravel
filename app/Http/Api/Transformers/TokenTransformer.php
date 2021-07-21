<?php

namespace App\Http\Api\Transformers;

use Flugg\Responder\Transformers\Transformer;

class TokenTransformer extends Transformer
{
    public function transform(string $token, int $ttl): array
    {
        return [
            'accessToken' => $token,
            'tokenType' => 'bearer',
            'expiresIn' => $ttl,
        ];
    }
}
