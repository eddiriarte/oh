<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

class PersonObject
{
    public function __construct(
        private string $firstName,
        private string $lastName
    ) {
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
