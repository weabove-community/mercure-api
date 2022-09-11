<?php

namespace App\Command;

use App\ElrondApi\TransactionService;
use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

#[AsCommand(name: 'elrond:transaction-import')]
class TransactionImportCommand extends Command
{
    /** @var TransactionService  */
    private $transactionService;

    protected function configure(): void
    {
        /*

        GSPACEAPE-08bc2b-2c7c
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


        $client = new Client();
        $queryParams = [
            'withScamInfo' => 'false',
            'status' => 'success',
            'token' => 'GSPACEAPE-08bc2b-2c7c',
            'order'=> 'desc'
        ];
        $response = $client->request('GET', 'https://api.elrond.com/transactions', ['query' => $queryParams]);
        $functions = [];
        $marketplaces = [];
        foreach (json_decode($response->getBody()->getContents(), true) as $dataTransaction) {
            dump($dataTransaction);
            $message = $dataTransaction['action']['arguments']["receiverAssets"]['name'] ?? null;
            $marketplaces[] = $dataTransaction['txHash'];
            $marketplaces[] = $message . ' ' . $dataTransaction['function'];
            $time  = $dataTransaction['timestamp'];
            $datetimeFormat = 'Y-m-d H:i:s';

            $date = new \DateTime();
            $date->setTimestamp($time);
            $marketplaces[] = $date->format($datetimeFormat);

            $data = explode("@",base64_decode($dataTransaction['data']));
            foreach ($data as $k => $value) {
                if ($k == 0) {continue;}
                dump($value, hexdec($value), hex2bin($value), '******************');

            }
            exit;

            ;
            /*
             *
             if ($dataTransaction['function'] == 'auctionToken') {
                continue;
            }
            $data = explode("@",base64_decode($dataTransaction['data']));
            $functions[] = count($data);

            /*
            if (!isset($functions[$dataTransaction['function']])) {
                $functions[$dataTransaction['function']] = 1;
            } else {
                $functions[$dataTransaction['function']]++;
            }
            /*


            $time  = $dataTransaction['timestamp'];
            $datetimeFormat = 'Y-m-d H:i:s';

            $date = new \DateTime();
            $date->setTimestamp($time);
            echo $date->format($datetimeFormat);
            dump(
                '******************',
                $dataTransaction['txHash'],
                $date->format('Y-m-d H:i:s'),
                $dataTransaction['function'],
            );
            */
        }
        /*$code ='RVNEVE5GVFRyYW5zZmVyQDQ3NTM1MDQxNDM0NTQxNTA0NTJkMzAzODYyNjMzMjYyQDJjN2NAMDFAMDAwMDAwMDAwMDAwMDAwMDA1MDBkM2IyODgyOGQ2MjA1MjEyNGYwN2RjZDUwZWQzMWIwODI1ZjYwZWVlMTUyNkA2YzY5NzM3NDY5NmU2N0A0ODJhMWM3MzAwMDgwMDAwQDQ4MmExYzczMDAwODAwMDBAQDQ1NDc0YzQ0QEA=';
        dump(base64_decode($code));
        $data = explode("@",base64_decode($code));
        foreach ($data as $k => $value) {
            if ($k == 0) {continue;}
            dump($value, hexdec($value), hex2bin($value), '******************');

        }
        */
        //$this->transactionService->get($queryParams);

            dump($marketplaces);
        return Command::SUCCESS;
    }
}
