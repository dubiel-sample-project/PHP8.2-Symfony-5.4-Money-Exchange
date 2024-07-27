<?php

namespace App\Domain\ValueObjects;

use App\Domain\Enums\Currency;
use App\Shared\Attributes\Author;

#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
readonly class Money
{
    public function __construct(
        private Currency $currency,
        private float $amount
    ) {
        if ($this->amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function add(Money $other): Money
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new \InvalidArgumentException('Currencies do not match.');
        }

        return new self($this->currency, $this->amount + $other->getAmount());
    }

    public function subtract(Money $other): Money
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new \InvalidArgumentException('Currencies do not match.');
        }

        return new self($this->currency, $this->amount - $other->getAmount());
    }

    public function withAmount(float $amount): self
    {
        return new self($this->currency, $amount);
    }
}
