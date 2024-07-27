<?php

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\Enums\Currency;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class CurrencyTest extends TestCase
{
    public function testValidCurrency(): void
    {
        $currency = Currency::EUR;
        $this->assertSame('EUR', $currency->value);
    }
}
