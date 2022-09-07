<?php

namespace App\Command;

use App\ElrondApi\TransactionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'elrond:transaction-import')]
class TransactionImportCommand extends Command
{
    /** @var TransactionService  */
    private $transactionService;

    protected function configure(): void
    {
        /*
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'Token identifier')
        ;
        */
    }

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queryParams = [];
        echo base64_decode('RVNEVE5GVFRyYW5zZmVyQDQ3NTM1MDQxNDM0NTQxNTA0NTJkMzAzODYyNjMzMjYyQDA4OGJAMDFAMDAwMDAwMDAwMDAwMDAwMDA1MDBmM2U4OTMwNTIzOTRmMmY1ZmUzOWM2NWUzMWMwMjRiMDBlN2Q5MjczYWQ4ZEA2MTc1NjM3NDY5NmY2ZTU0NmY2YjY1NmVAMDEzMTRmYjM3MDYyOTgwMDAwQDAyYjVlM2FmMTZiMTg4MDAwMEA2M2RmZWIxYUA0NTQ3NGM0NEBAQDYyZWQ1NzFh');
        //$this->transactionService->get($queryParams);
        return Command::SUCCESS;
    }
}
