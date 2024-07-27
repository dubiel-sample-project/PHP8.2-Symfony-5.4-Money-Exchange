<?php

namespace App\Application\Infrastructure\Repositories;

use App\Domain\Enums\Currency;
use App\Domain\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ValueObjects\ExchangeRate;
use App\Shared\Attributes\Author;

#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    private array $exchangeRates = [];

    public function __construct(array $exchangeRates)
    {
        $this->exchangeRates = $exchangeRates;
    }

    public function findRate(Currency $sourceCurrency, Currency $targetCurrency): ?ExchangeRate
    {
        $key = $sourceCurrency->value.'_'.$targetCurrency->value;
        $rate = $this->exchangeRates[$key] ?? null;

        if (null === $rate) {
            return null;
        }

        return new ExchangeRate($sourceCurrency, $targetCurrency, $rate);
    }
}
