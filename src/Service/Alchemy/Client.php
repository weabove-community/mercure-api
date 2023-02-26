<?php
namespace App\Service\Alchemy;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const API_KEY           = 'bSuCLA4e7wWfCXt7M4GrjsC-D4f2VOWF';
    const ALCHEMY_API_URI   = 'https://eth-mainnet.g.alchemy.com';

    const METHOD_GET_OWNER_FOR_COLLECTION   = 'getOwnersForCollection';
    const METHOD_GET_NFTS                   = 'getNFTs';
    const METHOD_GET_CONTRACTS_FOR_OWNER    = 'getContractsForOwner';

    /**
     * @param string $method
     * @return string
     */
    private function buildUri($method): string
    {
        return sprintf('%s/nft/v2/%s/%s',
            self::ALCHEMY_API_URI,
            self::API_KEY,
            $method);
    }
    /**
     * @param string $smartContract
     * @return Response
     * @throws GuzzleException
     */
    public function getOwnersForCollection($smartContract): Response
    {
        $url = sprintf('%s?%s',
            $this->buildUri(self::METHOD_GET_OWNER_FOR_COLLECTION),
            $this->buildQuery([
                'contractAddress' => $smartContract,
                'withTokenBalances' => 'true'
            ])
        );

        $client = new GuzzleClient();
        return $client->request('GET', $url);
    }

    /**
     * @param array $smartContracts
     * @param string $owner
     * @throws GuzzleException
     * @return Response
     */
    public function getNFTsCollectionsByOwner(array $smartContracts, string $owner): Response
    {
        $uri = $this->buildUri(self::METHOD_GET_NFTS);

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
     * @param string $owner
     * @return Response
     * @throws GuzzleException
     */
    public function getContractsForOwner(string $owner): Response
    {
        $uri = $this->buildUri(self::METHOD_GET_CONTRACTS_FOR_OWNER);
        $options = [
            'query' => $this->buildQuery([
                'owner' => $owner,
                'withMetadata' => 'false'
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
