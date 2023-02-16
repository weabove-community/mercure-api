<?php
namespace App\Service\Alchemy;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const API_KEY = 'bSuCLA4e7wWfCXt7M4GrjsC-D4f2VOWF';

    /**
     * @param string $smartContract
     * @param string $owner
     * @return Response
     */
    public function getNFTsCollectionsByOwner(string $smartContract, string $owner): Response
    {
        $uri = sprintf('https://eth-mainnet.g.alchemy.com/nft/v2/%s/getNFTs', self::API_KEY);
        $uri .= sprintf('?contractAddresses[]=%s&owner=%s&withMetadata=true', $smartContract, $owner);
        $client = new GuzzleClient();

        return $client->request('GET', $uri);
    }
}
