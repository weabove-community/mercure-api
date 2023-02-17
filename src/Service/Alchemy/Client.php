<?php
namespace App\Service\Alchemy;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const API_KEY = 'bSuCLA4e7wWfCXt7M4GrjsC-D4f2VOWF';

    /**
     * @param array $smartContracts
     * @param string $owner
     * @return Response
     */
    public function getNFTsCollectionsByOwner(array $smartContracts, string $owner): Response
    {
        $uri = sprintf('https://eth-mainnet.g.alchemy.com/nft/v2/%s/getNFTs', self::API_KEY);

        $options = [
            'query' => [
                'contractAddresses' => $smartContracts,
                'owner' => $owner,
                'withMetadata' => 'true'
            ]
        ];

        $client = new GuzzleClient();
        return $client->request('GET', $uri, $options);
    }
}
