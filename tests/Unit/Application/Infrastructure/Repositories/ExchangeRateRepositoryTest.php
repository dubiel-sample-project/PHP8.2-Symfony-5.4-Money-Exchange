<?php

namespace App\Tests\Unit\Application\Infrastructure\Repositories;

use App\Application\Infrastructure\Repositories\ExchangeRateRepository;
use App\Domain\Enums\Currency;
use App\Domain\ValueObjects\ExchangeRate;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExchangeRateRepository::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeRateRepositoryTest extends TestCase
{
    private ExchangeRateRepository $repository;

    protected function setUp(): void
    {
        $exchangeRates = [
            'EUR_GBP' => 1.50,
            'GBP_EUR' => 0.75,
        ];

        $this->repository = new ExchangeRateRepository($exchangeRates);
    }

    public function testFindRateReturnsCorrectExchangeRate(): void
    {
        $currencyEUR = Currency::from('EUR');
        $currencyGBP = Currency::from('GBP');

        $exchangeRate = $this->repository->findRate($currencyEUR, $currencyGBP);

        $this->assertInstanceOf(ExchangeRate::class, $exchangeRate);
        $this->assertEquals($currencyEUR, $exchangeRate->getSourceCurrency());
        $this->assertEquals($currencyGBP, $exchangeRate->getTargetCurrency());
        $this->assertEquals(1.50, $exchangeRate->getRate());
    }

    public function testFindRateReturnsNullForNonExistentRate(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyUSD = Currency::from('USD');

        $exchangeRate = $this->repository->findRate($currencyUSD, $currencyGBP);

        $this->assertNull($exchangeRate);
    }

    public function testFindRateHandlesMultipleRates(): void
    {
        $currencyEUR = Currency::from('EUR');
        $currencyGBP = Currency::from('GBP');
        $currencyUSD = Currency::from('USD');

        $exchangeRateEURToGBP = $this->repository->findRate($currencyEUR, $currencyGBP);
        $exchangeRateGBPToEUR = $this->repository->findRate($currencyGBP, $currencyEUR);

        $this->assertEquals(1.50, $exchangeRateEURToGBP->getRate());
        $this->assertEquals(0.75, $exchangeRateGBPToEUR->getRate());

        $this->assertNull($this->repository->findRate($currencyUSD, $currencyGBP));
    }
}
