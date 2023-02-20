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
            'query' => $this->buildQuery([
                'contractAddresses' => $smartContracts,
                'owner' => $owner,
                'withMetadata' => 'true'
            ])
        ];

        $client = new GuzzleClient();
        return $client->request('GET', $uri, $options);
    }

    /**
     * @param array $options
     * @return string
     * @throws \Exception
     */
    private function buildQuery(array $options)
    {
        $query = [];
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                foreach($value as $element) {
                    if (is_array($element)) {
                        throw new \Exception('l option %s a pour valeur un array imbriqué');
                    }
                    $query[] = sprintf('%s[]=%s', $key, $element);
                }
                continue;
            }
            $query[] = sprintf('%s=%s', $key, $value);
        }
        return implode('&', $query);
    }
}
