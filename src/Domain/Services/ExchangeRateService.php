<?php

namespace App\Domain\Services;

use App\Domain\Enums\Currency;
use App\Domain\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ValueObjects\ExchangeRate;
use App\Shared\Attributes\Author;

#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
readonly class ExchangeRateService
{
    public function __construct(
        private ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {
    }

    /**
     * Get the exchange rate for the given currencies.
     *
     * @throws \InvalidArgumentException
     */
    public function getRate(Currency $sourceCurrency, Currency $targetCurrency): ExchangeRate
    {
        $exchangeRate = $this->exchangeRateRepository->findRate($sourceCurrency, $targetCurrency);

        if (!$exchangeRate) {
            throw new \InvalidArgumentException('Exchange rate not found.');
        }

        return $exchangeRate;
    }
}
