<?php

namespace App\Command;

use App\Repository\TokenRepository;
use App\Service\Blockchain\ERC20\WeAbove\ProcessStakingGRV;
use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'we-above:staking:estimate')]
class EstimateGRVByWalletCommand extends Command
{
    private ProcessStakingGRV $processStakingGRV;

    public function __construct(
        ProcessStakingGRV $processStakingGRV
    )
    {
        $this->processStakingGRV = $processStakingGRV;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('wallet', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sumPrime = $this->processStakingGRV->getPrimeTokensFromWallet($input->getArgument('wallet'));
        $sumOrdos = $this->processStakingGRV->getOrdosTokensFromWallet($input->getArgument('wallet'));

        $output->writeln(sprintf('Prime: %s', $sumPrime));
        $output->writeln(sprintf('Ordos: %s', $sumOrdos));
        $output->writeln(sprintf('Total: %s', $sumOrdos + $sumPrime));

        return Command::SUCCESS;
    }
}
