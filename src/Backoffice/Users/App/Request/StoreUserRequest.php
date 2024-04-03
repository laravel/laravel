<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Lightit\Backoffice\Users\Domain\DataTransferObjects\UserDto;

class StoreUserRequest extends FormRequest
{
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';

    public function rules(): array
    {
        return [
            self::NAME => ['required'],
            self::EMAIL => ['required', 'email:strict'],
            self::PASSWORD => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            name: $this->string(self::NAME)->toString(),
            email: $this->string(self::EMAIL)->toString(),
            password: $this->string(self::PASSWORD)->toString(),
        );
    }
}
