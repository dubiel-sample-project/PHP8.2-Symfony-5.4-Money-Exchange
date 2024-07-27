<?php

namespace App\Tests\Unit\Application\Services;

use App\Application\Services\ExchangeService;
use App\Domain\Enums\Currency;
use App\Domain\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ValueObjects\ExchangeRate;
use App\Domain\ValueObjects\Money;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExchangeService::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeServiceTest extends TestCase
{
    public function testPurchaseSuccessful(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyEUR = Currency::from('EUR');
        $exchangeRate = new ExchangeRate($currencyGBP, $currencyEUR, 1.25);

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository->method('findRate')
            ->with($currencyGBP, $currencyEUR)
            ->willReturn($exchangeRate);

        $exchangeService = new ExchangeService($exchangeRateRepository, 0.10, 0.10);
        $moneyGBP = new Money($currencyGBP, 100.00);
        $exchangedMoney = $exchangeService->purchase($moneyGBP, $currencyEUR);

        $this->assertSame($currencyEUR, $exchangedMoney->getCurrency());
        $this->assertEqualsWithDelta(112.50, $exchangedMoney->getAmount(), 0.01);
    }

    public function testSellSuccessful(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyEUR = Currency::from('EUR');
        $exchangeRate = new ExchangeRate($currencyGBP, $currencyEUR, 1.25);

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository->method('findRate')
            ->with($currencyGBP, $currencyEUR)
            ->willReturn($exchangeRate);

        $exchangeService = new ExchangeService($exchangeRateRepository, 0.10, 0.10);
        $moneyGBP = new Money($currencyGBP, 100.0);
        $exchangedMoney = $exchangeService->sell($moneyGBP, $currencyEUR);

        $this->assertSame($currencyEUR, $exchangedMoney->getCurrency());
        $this->assertEqualsWithDelta(137.50, $exchangedMoney->getAmount(), 0.01);
    }

    public function testPurchaseRateNotFound(): void
    {
        $currencyUSD = Currency::from('USD');
        $currencyEUR = Currency::from('EUR');

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository->method('findRate')
            ->with($currencyUSD, $currencyEUR)
            ->willReturn(null);

        $exchangeService = new ExchangeService($exchangeRateRepository, 0.10, 0.10);
        $moneyUSD = new Money($currencyUSD, 100.0);

        $this->expectException(\InvalidArgumentException::class);
        $exchangeService->purchase($moneyUSD, $currencyEUR);
    }

    public function testSellRateNotFound(): void
    {
        $currencyUSD = Currency::from('USD');
        $currencyEUR = Currency::from('EUR');

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository->method('findRate')
            ->with($currencyUSD, $currencyEUR)
            ->willReturn(null);

        $exchangeService = new ExchangeService($exchangeRateRepository, 0.10, 0.10);
        $moneyUSD = new Money($currencyUSD, 100.0);

        $this->expectException(\InvalidArgumentException::class);
        $exchangeService->sell($moneyUSD, $currencyEUR);
    }
}
