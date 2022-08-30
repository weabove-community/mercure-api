<?php

namespace App\ElrondApi;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class CollectionService
{

    public function count($code): ResponseInterface
    {
        $client = new Client();
        return $client->request('GET', sprintf('https://api.elrond.com/collections/%s/nfts/count', $code));
    }

    public function get($code): ResponseInterface
    {
        $client = new Client();
        return $client->request('GET', sprintf('https://api.elrond.com/collections/%s', $code));
    }

    public function getNftsCollection($code, $queryParams = array()): ResponseInterface
    {
        $client = new Client();
        if (!isset($queryParams['withScamInfo'])) {
            $queryParams['withScamInfo'] = 'false';
        }
        if (!isset($queryParams['computeScamInfo'])) {
            $queryParams['computeScamInfo'] = 'false';
        }

        return $client->request('GET', sprintf('https://api.elrond.com/collections/%s/nfts', $code), [
            'query' => $queryParams
        ]);
    }
}
