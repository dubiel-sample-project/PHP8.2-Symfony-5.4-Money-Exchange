<?php

namespace App\Shared\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
readonly class Author
{
    public function __construct(public string $name, public string $email)
    {
    }
}
