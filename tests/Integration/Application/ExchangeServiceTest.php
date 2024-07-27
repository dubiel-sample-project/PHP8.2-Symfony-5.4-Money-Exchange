<?php

namespace App\Tests\Integration\Application;

use App\Application\Infrastructure\Repositories\ExchangeRateRepository;
use App\Application\Services\ExchangeService;
use App\Domain\Enums\Currency;
use App\Domain\ValueObjects\Money;
use App\Shared\Attributes\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(ExchangeService::class)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeServiceTest extends KernelTestCase
{
    private ExchangeService $exchangeService;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $exchangeRates = self::getContainer()->getParameter('exchange_rates');
        $purchaseFee = self::getContainer()->getParameter('purchase_fee');
        $salesCredit = self::getContainer()->getParameter('sales_credit');

        $exchangeRateRepository = new ExchangeRateRepository($exchangeRates);
        $this->exchangeService = new ExchangeService($exchangeRateRepository, $purchaseFee, $salesCredit);
    }

    public function testSell100EURForGBP(): void
    {
        $currencyEUR = Currency::from('EUR');
        $currencyGBP = Currency::from('GBP');
        $moneyEUR = new Money($currencyEUR, 100.00);

        $exchangedMoney = $this->exchangeService->sell($moneyEUR, $currencyGBP);

        $this->assertSame(Currency::GBP, $exchangedMoney->getCurrency());
        $this->assertEqualsWithDelta(158.35, $exchangedMoney->getAmount(), 0.01);
    }

    public function testPurchase100GBPWithEUR(): void
    {
        $currencyEUR = Currency::from('EUR');
        $currencyGBP = Currency::from('GBP');
        $moneyEUR = new Money($currencyEUR, 100.00);

        $exchangedMoney = $this->exchangeService->purchase($moneyEUR, $currencyGBP);

        $this->assertSame(Currency::GBP, $exchangedMoney->getCurrency());
        $this->assertEqualsWithDelta(155.21, $exchangedMoney->getAmount(), 0.01);
    }

    public function testSell100GBPForEUR(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyEUR = Currency::from('EUR');
        $moneyGBP = new Money($currencyGBP, 100.00);

        $exchangedMoney = $this->exchangeService->sell($moneyGBP, $currencyEUR);

        $this->assertSame(Currency::EUR, $exchangedMoney->getCurrency());
        $this->assertEqualsWithDelta(155.86, $exchangedMoney->getAmount(), 0.01);
    }

    public function testPurchases100EURWithGBP(): void
    {
        $currencyGBP = Currency::from('GBP');
        $currencyEUR = Currency::from('EUR');
        $moneyGBP = new Money($currencyGBP, 100.00);

        $exchangedMoney = $this->exchangeService->purchase($moneyGBP, $currencyEUR);

        $this->assertSame(Currency::EUR, $exchangedMoney->getCurrency());
        $this->assertEqualsWithDelta(152.78, $exchangedMoney->getAmount(), 0.01);
    }
}
