<?php

namespace App\Command;

use App\ElrondApi\TransactionService;
use App\Entity\Transaction;
use Doctrine\Persistence\ManagerRegistry;
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
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'Token identifier')
            ->addArgument('function', InputArgument::REQUIRED, 'Transaction function')
        ;
    }

    public function __construct(TransactionService $transactionService,
                                ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
        $this->transactionService = $transactionService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $ori = 'RVNEVE5GVFRyYW5zZmVyQDQ3NTM1MDQxNDM0NTQxNTA0NTJkMzAzODYyNjMzMjYyQDI0MmVAMDFAMDAwMDAwMDAwMDAwMDAwMDA1MDBkM2IyODgyOGQ2MjA1MjEyNGYwN2RjZDUwZWQzMWIwODI1ZjYwZWVlMTUyNkA2YzY5NzM3NDY5NmU2N0AwMWJjMTZkNjc0ZWM4MDAwMDBAMDFiYzE2ZDY3NGVjODAwMDAwQEA0NTQ3NGM0NEBA';
        $data = explode("@",base64_decode($ori));

        dump($data);
        foreach ($data as $k => $value) {
            if ($k == 0) {continue;}
            dump($value, hexdec($value), hex2bin($value), '******************');

        }
        dump(hexdec($data[6])/1000000000000000000);
        /*
         *
         dump(hex2bin($data[1]));
        dump(hexdec($data[2]));
        dump(hexdec($data[3]));
        dump($data[4]);
        dump(hex2bin($data[5]));
        dump(hexdec($data[6])/1000000000000000000);
*/

        exit;

    /*
     * ESDTNFTTransfer
@IDENTIFIANT_COLLECTION
@NONCE_DU_TOKEN
@AMOUNT | 1 vue que NFT
@000000000000000005006946c62a71f1f2af4b0b81d897126133d09fd38916ae
@auctionToken
6150000000000000000
@TOKEN_UTILISE_POUR_ACHAT
     */







        $client = new Client();
        $queryParams = [
            'withScamInfo' => 'false',
            'status' => 'success',
            'function' => $input->getArgument('function'),
            'token' => $input->getArgument('identifier'),
            //'token' => 'GSPACEAPE-08bc2b-2c7c',
            //'token' => 'EAPES-8f3c1f-1a07',
            'order'=> 'desc'
        ];
        $response = $client->request('GET', 'https://api.elrond.com/transactions', ['query' => $queryParams]);

        foreach (json_decode($response->getBody()->getContents(), true) as $dataTransaction) {
            $transaction = new Transaction();
            $transaction
                ->setTimestamp($dataTransaction['timestamp'])
                ->setFunction($dataTransaction['function'])
                ->setTxHash($dataTransaction['txHash'])
                ->setTicker($dataTransaction['action']['arguments']['transfers'][0]['ticker'])
                ->setReceiver($dataTransaction['action']['arguments']['receiver'])
                ->setIdentifier($dataTransaction['action']['arguments']['transfers'][0]['identifier'])
            ;

            $this->entityManager->persist($transaction);


            /*
            $message = $dataTransaction['action']['arguments']["receiverAssets"]['name'] ?? null;
            $marketplaces[] = $dataTransaction['txHash'];
            if (isset($dataTransaction['function'])) {
                $marketplaces[] = $message . ' ' . $dataTransaction['function'];
            }

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

        $this->entityManager->flush();
        /*$code ='RVNEVE5GVFRyYW5zZmVyQDQ3NTM1MDQxNDM0NTQxNTA0NTJkMzAzODYyNjMzMjYyQDJjN2NAMDFAMDAwMDAwMDAwMDAwMDAwMDA1MDBkM2IyODgyOGQ2MjA1MjEyNGYwN2RjZDUwZWQzMWIwODI1ZjYwZWVlMTUyNkA2YzY5NzM3NDY5NmU2N0A0ODJhMWM3MzAwMDgwMDAwQDQ4MmExYzczMDAwODAwMDBAQDQ1NDc0YzQ0QEA=';
        dump(base64_decode($code));
        $data = explode("@",base64_decode($code));
        foreach ($data as $k => $value) {
            if ($k == 0) {continue;}
            dump($value, hexdec($value), hex2bin($value), '******************');

        }
        */
        //$this->transactionService->get($queryParams);


        return Command::SUCCESS;
    }

    public function listing(string $data)
    {

    }
}
