<?php

namespace App\Application\Services;

use App\Domain\Enums\Currency;
use App\Domain\Repositories\ExchangeRateRepositoryInterface;
use App\Domain\ValueObjects\Money;
use App\Shared\Attributes\Author;

#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
readonly class ExchangeService
{
    public function __construct(
        private ExchangeRateRepositoryInterface $exchangeRateRepository,
        private float $purchaseFee,
        private float $salesCredit
    ) {
    }

    /**
     * Purchases money from one currency to another.
     *
     * @throws \InvalidArgumentException
     */
    public function purchase(Money $money, Currency $targetCurrency): Money
    {
        $exchangeRate = $this->exchangeRateRepository->findRate($money->getCurrency(), $targetCurrency);

        if (!$exchangeRate) {
            throw new \InvalidArgumentException('Exchange rate not found.');
        }

        $moneyAfterFee = $this->applyPurchaseFee($money);

        return $exchangeRate->convert($moneyAfterFee);
    }

    /**
     * Sells money from one currency to another.
     *
     * @throws \InvalidArgumentException
     */
    public function sell(Money $money, Currency $targetCurrency): Money
    {
        $exchangeRate = $this->exchangeRateRepository->findRate($money->getCurrency(), $targetCurrency);

        if (!$exchangeRate) {
            throw new \InvalidArgumentException('Exchange rate not found.');
        }

        $moneyAfterCredit = $this->applySellCredit($money);

        return $exchangeRate->convert($moneyAfterCredit);
    }

    private function applyPurchaseFee(Money $money): Money
    {
        $fee = $money->getAmount() * $this->purchaseFee;
        $amountAfterFee = $money->getAmount() - $fee;

        return $money->withAmount($amountAfterFee);
    }

    private function applySellCredit(Money $money): Money
    {
        $credit = $money->getAmount() * $this->salesCredit;
        $amountAfterCredit = $money->getAmount() + $credit;

        return $money->withAmount($amountAfterCredit);
    }
}
