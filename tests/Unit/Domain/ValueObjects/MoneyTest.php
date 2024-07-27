<?php

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\Enums\Currency;
use App\Domain\ValueObjects\Money;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Money::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class MoneyTest extends TestCase
{
    public function testValidMoney(): void
    {
        $money = new Money(Currency::EUR, 100.0);

        $this->assertSame(Currency::EUR, $money->getCurrency());
        $this->assertSame(100.0, $money->getAmount());
    }

    public function testInvalidAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(Currency::EUR, -100.0);
    }

    public function testAddition(): void
    {
        $money1 = new Money(Currency::EUR, 100.0);
        $money2 = new Money(Currency::EUR, 50.0);

        $result = $money1->add($money2);

        $this->assertSame(150.0, $result->getAmount());
    }

    public function testSubtraction(): void
    {
        $money1 = new Money(Currency::EUR, 100.0);
        $money2 = new Money(Currency::EUR, 50.0);

        $result = $money1->subtract($money2);

        $this->assertSame(50.0, $result->getAmount());
    }

    public function testCurrencyMismatchWithAdd(): void
    {
        $moneyGBP = new Money(Currency::GBP, 100.0);
        $moneyEUR = new Money(Currency::EUR, 50.0);

        $this->expectException(\InvalidArgumentException::class);
        $moneyGBP->add($moneyEUR);
    }

    public function testCurrencyMismatchWithSubtract(): void
    {
        $moneyGBP = new Money(Currency::GBP, 100.0);
        $moneyEUR = new Money(Currency::EUR, 50.0);

        $this->expectException(\InvalidArgumentException::class);
        $moneyGBP->subtract($moneyEUR);
    }

    public function testWithAmount(): void
    {
        $moneyGBP = new Money(Currency::GBP, 100.0);

        $result = $moneyGBP->withAmount(50.0);

        $this->assertSame(Currency::GBP, $result->getCurrency());
        $this->assertSame(50.0, $result->getAmount());
    }
}
