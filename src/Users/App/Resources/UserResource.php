<?php

declare(strict_types=1);

namespace Lightit\Users\App\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Users\Domain\Models\User;

/**
 * @mixin User
 */
#[SchemaName('User')]
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email_address' => $this->email,
        ];
    }
}
