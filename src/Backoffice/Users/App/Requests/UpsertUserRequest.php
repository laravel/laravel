<?php

declare(strict_types=1);

namespace Lightit\Backoffice\Users\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lightit\Backoffice\Users\Domain\DataTransferObjects\UserDto;
use Lightit\Backoffice\Users\Domain\Models\User;

class UpsertUserRequest extends FormRequest
{
    public const NAME = 'name';

    public const EMAIL = 'email_address';

    public const PASSWORD = 'password';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            self::NAME => ['required'],
            self::EMAIL => [
                'required',
                Rule::email()
                    ->strict(),
                Rule::unique(User::class, 'email')
                    ->ignore($user?->id),
            ],
            self::PASSWORD => ['required', Password::min(8)->letters()->numbers(), 'confirmed'],
        ];
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            name: $this->string(self::NAME)->toString(),
            emailAddress: $this->string(self::EMAIL)->toString(),
            password: $this->string(self::PASSWORD)->toString(),
        );
    }
}
