<?php

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\Enums\Currency;
use App\Domain\ValueObjects\ExchangeRate;
use App\Domain\ValueObjects\Money;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExchangeRate::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeRateTest extends TestCase
{
    public function testValidExchangeRate(): void
    {
        $exchangeRate = new ExchangeRate(Currency::GBP, Currency::EUR, 0.75);

        $this->assertSame(Currency::GBP, $exchangeRate->getSourceCurrency());
        $this->assertSame(Currency::EUR, $exchangeRate->getTargetCurrency());
        $this->assertSame(0.75, $exchangeRate->getRate());
    }

    public function testInvalidRate(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ExchangeRate(Currency::GBP, Currency::EUR, -0.75);
    }

    public function testConvertMoney(): void
    {
        $exchangeRate = new ExchangeRate(Currency::GBP, Currency::EUR, 1.50);
        $moneyGBP = new Money(Currency::GBP, 100.0);

        $convertedMoney = $exchangeRate->convert($moneyGBP);

        $this->assertSame(Currency::EUR, $convertedMoney->getCurrency());
        $this->assertEqualsWithDelta(150, $convertedMoney->getAmount(), 0.01);
    }

    public function testConvertMoneyCurrencyMismatch(): void
    {
        $exchangeRate = new ExchangeRate(Currency::GBP, Currency::EUR, 0.75);
        $moneyUSD = new Money(Currency::USD, 100);

        $this->expectException(\InvalidArgumentException::class);
        $exchangeRate->convert($moneyUSD);
    }
}
