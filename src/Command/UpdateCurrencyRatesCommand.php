<?php

namespace App\Command;

use App\Service\CurrencyRateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCurrencyRatesCommand extends Command
{

    protected static $defaultName = 'app:update-currency-rates';

    private CurrencyRateService $service;

    public function __construct(CurrencyRateService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Fetches and updates currency rates from the external API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->service->updateRates();
            $output->writeln('<info>Currency rates updated successfully.</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error updating rates: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}