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

    public function __toString(): string
    {
        return $this->fullName();
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function fullName(string $separator = ' '): string
    {
        return $this->firstName . $separator . $this->lastName;
    }

    public function sortableName(): string
    {
        return "$this->lastName, $this->firstName";
    }
}
