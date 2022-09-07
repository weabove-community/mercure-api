<?php

namespace App\ElrondApi;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TransactionService
{
    public function count($code): ResponseInterface
    {
        $client = new Client();
        return $client->request('GET', sprintf('https://api.elrond.com/transactions/%s/nfts/count', $code));
    }

    public function getHash($txHash): ResponseInterface
    {
        $client = new Client();
        return $client->request('GET', sprintf('https://api.elrond.com/transactions/%s', $txHash));
    }

    public function get($queryParams = array()): ResponseInterface
    {
        $client = new Client();
        if (!isset($queryParams['withScamInfo'])) {
            $queryParams['withScamInfo'] = 'false';
        }

        return $client->request('GET', 'https://api.elrond.com/transactions', [
            'query' => $queryParams
        ]);
    }
}