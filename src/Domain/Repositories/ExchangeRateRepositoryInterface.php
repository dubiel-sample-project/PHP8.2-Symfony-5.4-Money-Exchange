<?php

namespace App\Domain\Repositories;

use App\Domain\Enums\Currency;
use App\Domain\ValueObjects\ExchangeRate;
use App\Shared\Attributes\Author;

#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
interface ExchangeRateRepositoryInterface
{
    public function findRate(Currency $sourceCurrency, Currency $targetCurrency): ?ExchangeRate;
}
