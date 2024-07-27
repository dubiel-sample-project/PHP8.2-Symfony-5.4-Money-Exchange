<?php

namespace App\Application\Commands;

use App\Application\Services\ExchangeService;
use App\Domain\Enums\Currency;
use App\Domain\ValueObjects\Money;
use App\Shared\Attributes\Author;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'app:exchange-money',
    description: 'Exchanges Money.',
    aliases: [],
    hidden: false
)]
#[Author('Maciej Dubiel', 'munich55555@gmail.com')]
class ExchangeMoneyCommand extends Command
{
    private const PURCHASE = 'purchase';
    private const SELL = 'sell';

    public function __construct(private readonly ExchangeService $exchangeService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Exchange a given amount of money from one currency to another.')
            ->addArgument('amount', InputArgument::REQUIRED, 'The amount of money to exchange')
            ->addArgument('source_currency', InputArgument::REQUIRED, 'The currency code of the source currency')
            ->addArgument('target_currency', InputArgument::REQUIRED, 'The currency code of the target currency');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $amount = (float) $input->getArgument('amount');
        $sourceCurrencyCode = $input->getArgument('source_currency');
        $targetCurrencyCode = $input->getArgument('target_currency');

        try {
            $sourceCurrency = Currency::tryFrom($sourceCurrencyCode);
            if (!$sourceCurrency) {
                throw new \InvalidArgumentException('Invalid source currency');
            }

            $targetCurrency = Currency::tryFrom($targetCurrencyCode);
            if (!$targetCurrency) {
                throw new \InvalidArgumentException('Invalid target currency');
            }

            $money = new Money($sourceCurrency, $amount);

            $helper = $this->getHelper('question');

            $choice1 = 'Purchase:';
            $choice2 = 'Sell:';
            $choice = new ChoiceQuestion('<bg=cyan>Please select an action</>',
                [$choice1, $choice2], 2);
            $answer = $helper->ask($input, $output, $choice);
            $action = match ($answer) {
                2, $choice2 => self::SELL,
                0, $choice1 => self::PURCHASE
            };

            if (!$action) {
                throw new \InvalidArgumentException('Invalid action');
            }

            $exchangedMoney = match ($action) {
                self::PURCHASE => $this->exchangeService->purchase($money, $targetCurrency),
                self::SELL => $this->exchangeService->sell($money, $targetCurrency)
            };

            $output->writeln(sprintf(
                'Amount: %.2f %s',
                $exchangedMoney->getAmount(),
                $exchangedMoney->getCurrency()->value
            ));

            return Command::SUCCESS;
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }
    }
}
