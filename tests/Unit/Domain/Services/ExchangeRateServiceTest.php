<?php

namespace App\Tests\Unit\Domain\Services;

use App\Domain\Enums\Currency;
use App\Domain\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\Services\ExchangeRateService;
use App\Domain\ValueObjects\ExchangeRate;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExchangeRateService::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeRateServiceTest extends TestCase
{
    public function testGetRateSuccessful(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyEUR = Currency::from('EUR');
        $exchangeRate = new ExchangeRate($currencyGBP, $currencyEUR, 0.75);

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository->method('findRate')
            ->with($currencyGBP, $currencyEUR)
            ->willReturn($exchangeRate);

        $exchangeRateService = new ExchangeRateService($exchangeRateRepository);
        $retrievedRate = $exchangeRateService->getRate($currencyGBP, $currencyEUR);

        $this->assertSame($exchangeRate, $retrievedRate);
    }

    public function testGetRateRateNotFound(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyEUR = Currency::from('EUR');

        $this->expectException(\InvalidArgumentException::class);

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository->method('findRate')
            ->with($currencyGBP, $currencyEUR)
            ->willReturn(null);

        $exchangeRateService = new ExchangeRateService($exchangeRateRepository);

        $this->expectException(\InvalidArgumentException::class);
        $exchangeRateService->getRate($currencyGBP, $currencyEUR);
    }
}
