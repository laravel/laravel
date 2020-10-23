<?php

namespace App\Jobs\Serializers;

use ProjectName\Entities\User;

class ExampleUserSerializer
{
    private ?int $id = null;
    private string $email;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }
}
