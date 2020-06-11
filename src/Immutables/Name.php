<?php

namespace ProjectName\Immutables;

class Name
{
    private string $firstName;
    private string $lastName;

    public function __construct(string $firstName, string $lastname)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastname;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
