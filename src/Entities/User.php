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
use Tymon\JWTAuth\Contracts\JWTSubject;

class User implements AuthenticatableContract, JWTSubject, CanResetPasswordContract
{
    use Authenticatable;
    use CanResetPassword;
    use Timestamps;
    use SoftDeletes;

    private ?int $id = null;
    private Name $name;
    private string $email;

    public function __construct(UserPayload $userPayload)
    {
        $this->changeEmail($userPayload->email());
        $this->setPassword($userPayload->password());
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

    public function changeEmail(string $email): void
    {
        $this->email = trim($email);
    }

    public function setPassword(string $plainPassword): void
    {
        $this->password = password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    public function changeName(string $firstName, string $lastName): void
    {
        $this->name = new Name(trim($firstName), trim($lastName));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
