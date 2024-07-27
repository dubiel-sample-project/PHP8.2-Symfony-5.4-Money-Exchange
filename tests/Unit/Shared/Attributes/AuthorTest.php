<?php

namespace App\Tests\Unit\Shared\Attributes;

use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Author::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class AuthorTest extends TestCase
{
    public function testValidAuthor(): void
    {
        $author = new Author('John Smith', 'dummy@example.com');

        $this->assertSame('John Smith', $author->name);
        $this->assertSame('dummy@example.com', $author->email);
    }
}
