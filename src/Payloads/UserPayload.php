<?php

namespace ProjectName\Payloads;

class UserPayload
{
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastname;

    public function __construct(string $email, string $password, string $firstName, string $lastname)
    {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastname = $lastname;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastname(): string
    {
        return $this->lastname;
    }
}
