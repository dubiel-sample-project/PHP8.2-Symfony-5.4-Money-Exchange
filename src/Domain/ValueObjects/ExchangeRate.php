<?php

namespace App\Domain\ValueObjects;

use App\Domain\Enums\Currency;
use App\Shared\Attributes\Author;

#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
readonly class ExchangeRate
{
    public function __construct(
        private Currency $sourceCurrency,
        private Currency $targetCurrency,
        private float $rate
    ) {
        if ($this->rate <= 0) {
            throw new \InvalidArgumentException('Exchange rate must be positive.');
        }
    }

    public function getSourceCurrency(): Currency
    {
        return $this->sourceCurrency;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function convert(Money $money): Money
    {
        if ($money->getCurrency() !== $this->sourceCurrency) {
            throw new \InvalidArgumentException('Currency mismatch.');
        }

        $convertedAmount = $money->getAmount() * $this->rate;

        return new Money($this->targetCurrency, $convertedAmount);
    }
}
