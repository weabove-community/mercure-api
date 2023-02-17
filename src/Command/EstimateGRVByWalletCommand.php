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
        $res = $this->processStakingGRV->getTokensFromWallet($input->getArgument('wallet'));

        dump($res);

        return Command::SUCCESS;
    }
}
