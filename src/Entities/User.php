<?php

namespace ProjectName\Entities;

use ProjectName\Immutables\Name;

class User
{
    private ?int $id = null;
    private Name $name;
    private string $username;
    private string $email;

    public function __construct(?int $id, string $firstName, string $lastname, string $username, string $email)
    {
        $this->id = $id;
        $this->name = new Name($firstName, $lastname);
        $this->username = $username;
        $this->email = $email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
