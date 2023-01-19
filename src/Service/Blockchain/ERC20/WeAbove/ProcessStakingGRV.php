<?php

namespace App\Service\Blockchain\ERC20\WeAbove;

use App\Entity\Token;
use App\Entity\TokenAttribute;
use App\Repository\TokenRepository;
use GuzzleHttp\Client;

class ProcessStakingGRV
{
    const ETHERSCAN_API_URL = 'https://api.etherscan.io/api';

    const LABEL_PRIME = 'prime';
    const LABEL_ORDOS = 'ordos-database';

    const COLLECTIONS =
        [
            self::LABEL_PRIME => '0xd0aaac09e7f9b794fafa9020a34ad5b906566a5c',
            self::LABEL_ORDOS => '0xd4b1a63cb167968abf039a858c3745228fff937d',
        ];

    const MECHANICS = [
        self::COLLECTIONS[self::LABEL_PRIME] => [
            'basic' => 5,
            'special' => 9,
            'unique' => 14,
        ],
        self::COLLECTIONS[self::LABEL_ORDOS] => [
            'basic' => 1,
            'special' => 3,
            'unique' => 7,
        ],
    ];

    /** @var string */
    private $etherscanApiKey;

    /** @var TokenRepository */
    private $tokenRepository;

    /**
     * @param string          $etherscanApiKey
     * @param TokenRepository $tokenRepository
     */
    public function __construct(string $etherscanApiKey, TokenRepository $tokenRepository)
    {
        $this->etherscanApiKey = $etherscanApiKey;
        $this->tokenRepository = $tokenRepository;
    }

    public function isSpecial(Token $token): bool
    {
        /** @var TokenAttribute $tokenAttribute */
        foreach ($token->getTokenAttributes() as $tokenAttribute) {
            if ($tokenAttribute->getAttribute()->getTraitType()->getName() != 'Special') {
                continue;
            }

            return $tokenAttribute->getAttribute()->getValue() != null;
        }

        return false;
    }

    public function isUnique(Token $token): bool
    {
        /** @var TokenAttribute $tokenAttribute */
        foreach ($token->getTokenAttributes() as $tokenAttribute) {
            if ($tokenAttribute->getAttribute()->getTraitType()->getName() != 'Unique') {
                continue;
            }

            return $tokenAttribute->getAttribute()->getValue() != null;
        }

        return false;
    }

    public function isLvl3(Token $token): bool
    {
        /** @var TokenAttribute $tokenAttribute */
        foreach ($token->getTokenAttributes() as $tokenAttribute) {
            if ($tokenAttribute->getAttribute()->getTraitType()->getName() != 'Element') {
                continue;
            }

            if (!str_contains($tokenAttribute->getAttribute()->getValue(), 'lvl.3')) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function isSpecialBackground(Token $token): bool
    {
        /** @var TokenAttribute $tokenAttribute */
        foreach ($token->getTokenAttributes() as $tokenAttribute) {
            if ($tokenAttribute->getAttribute()->getTraitType()->getName() != 'Background') {
                continue;
            }

            if ($tokenAttribute->getAttribute()->getValue() === 'off-white' ||
                $tokenAttribute->getAttribute()->getValue() === 'Blue' ||
                is_null($tokenAttribute->getAttribute()->getValue())) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function isFreo(Token $token): bool
    {
        /** @var TokenAttribute $tokenAttribute */
        foreach ($token->getTokenAttributes() as $tokenAttribute) {
            if ($tokenAttribute->getAttribute()->getTraitType()->getName() != 'Faction') {
                continue;
            }

            if (!str_contains($tokenAttribute->getAttribute()->getValue(), 'Freo')) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param Token $token
     * @param $identifier
     * @return int
     */
    public function processBase(Token $token, $identifier): int
    {
        if ($this->isUnique($token)) {
            return self::MECHANICS[$identifier]['unique'];
        }

        if ($this->isSpecial($token)) {
            return self::MECHANICS[$identifier]['special'];
        }

        return self::MECHANICS[$identifier]['basic'];
    }

    /**
     * @param Token $token
     * @return int
     */
    public function processBonus(Token $token)
    {
        $sum = 0;
        if ($this->isSpecialBackground($token)) {
            $sum +=1;
        }

        if ($this->isLvl3($token)) {
            $sum +=1;
        }

        if ($this->isFreo($token)) {
            $sum +=1;
        }

        return $sum;
    }

    public function process($tokens, $identifier): array
    {
        $sum = 0;
        $details = [];
        foreach ($tokens as $token) {
            /** @var Token $token */
            $sumBase = $this->processBase($token, $identifier);
            $sumBonus = $this->processBonus($token);
            $details[$token->getToken()]['rewards'] = $sumBase + $sumBonus;
            $details[$token->getToken()]['img'] = $token->getImageUrl();
            $sum += $details[$token->getToken()]['rewards'];
        }

        return ['sum' => $sum, 'details' => $details];
    }

    public function getTokensFromWallet($wallet, $smartContractAddress): array
    {
        $client = new Client();
        $response = $client->request('GET', self::ETHERSCAN_API_URL, [
            'query' => [
                'module' => 'account',
                'action' => 'addresstokennftinventory',
                'address' => $wallet,
                'contractaddress' => $smartContractAddress,
                'apikey' => $this->etherscanApiKey
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $tokenIds = [];
        foreach ($data['result'] as $tokenData) {
            $tokenIds[] = $tokenData['TokenId'];
        }

        $tokens = $this->tokenRepository->findByTokenIdsAndCollection($smartContractAddress, $tokenIds);
        return $this->process($tokens, $smartContractAddress);
    }

    public function getPrimeTokensFromWallet($wallet): array
    {
        return $this->getTokensFromWallet($wallet, self::COLLECTIONS[self::LABEL_PRIME]);
    }

    public function getOrdosTokensFromWallet($wallet): array
    {
        return $this->getTokensFromWallet($wallet, self::COLLECTIONS[self::LABEL_ORDOS]);
    }
}
