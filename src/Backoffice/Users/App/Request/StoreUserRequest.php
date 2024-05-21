<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lightit\Backoffice\Users\Domain\DataTransferObjects\UserDto;
use Lightit\Backoffice\Users\Domain\Models\User;

class StoreUserRequest extends FormRequest
{
    public const NAME = 'name';

    public const EMAIL = 'email';

    public const PASSWORD = 'password';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::NAME => ['required'],
            self::EMAIL => ['required', Rule::email()->strict(), Rule::unique((new User())->getTable())],
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
