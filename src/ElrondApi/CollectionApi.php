<?php

namespace App\ElrondApi;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class CollectionApi
{

    const API_URL = 'https://api.elrond.com/collections';

    public function count(string $identifier): ResponseInterface
    {
        $client = new Client();
        return $client->request('GET', sprintf('%s/%s/nfts/count', self::API_URL, $identifier));
    }

    public function get($code): ResponseInterface
    {
        $client = new Client();
        return $client->request('GET', sprintf('https://api.elrond.com/collections/%s', $code));
    }

    public function getNftsCollection($identifier, $queryParams = array()): ResponseInterface
    {
        $client = new Client();
        if (!isset($queryParams['withScamInfo'])) {
            $queryParams['withScamInfo'] = 'false';
        }
        if (!isset($queryParams['computeScamInfo'])) {
            $queryParams['computeScamInfo'] = 'false';
        }

        $queryParams['withOwner'] = 'false';
        $queryParams['withSupply'] = 'false';

        return $client->request('GET', sprintf('https://api.elrond.com/collections/%s/nfts', $identifier), [
            'query' => $queryParams
        ]);
    }
}
