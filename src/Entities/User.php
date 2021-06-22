<?php

namespace ProjectName\Entities;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\ORM\Auth\Authenticatable;
use ProjectName\Immutables\Name;
use ProjectName\Payloads\UserPayload;
use ProjectName\Payloads\UserUpdatePayload;
use ProjectName\Utils\JWTSubjectable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User implements AuthenticatableContract, JWTSubject, CanResetPasswordContract
{
    use Timestamps;
    use SoftDeletes;
    use Authenticatable;
    use JWTSubjectable;
    use CanResetPassword;

    private ?int $id = null;
    private Name $name;
    private string $email;

    public function __construct(UserPayload $userPayload)
    {
        $this->changeEmail($userPayload->email());
        $this->changePassword($userPayload->password());
        $this->changeName($userPayload->firstName(), $userPayload->lastname());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function update(UserUpdatePayload $userPayload): void
    {
        if ($userPayload->email()) {
            $this->changeEmail($userPayload->email());
        }

        if ($userPayload->password()) {
            $this->setPassword($userPayload->password());
        }

        if ($userPayload->firstName() && $userPayload->lastname()) {
            $this->changeName($userPayload->firstName(), $userPayload->lastname());
        }
    }

    private function changeEmail(string $email): void
    {
        $this->email = trim($email);
    }

    private function changePassword(string $plainPassword): void
    {
        $this->password = password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    private function changeName(string $firstName, string $lastName): void
    {
        $this->name = new Name(trim($firstName), trim($lastName));
    }
}
