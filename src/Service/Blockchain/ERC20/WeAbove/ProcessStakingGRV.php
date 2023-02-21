<?php

namespace App\Service\Blockchain\ERC20\WeAbove;

use App\Entity\Token;
use App\Entity\TokenAttribute;
use App\Repository\TokenRepository;
use App\Service\Alchemy\Client as AlchemyClient;

class ProcessStakingGRV
{
    const LABEL_PRIME = 'prime';
    const LABEL_ORDOS = 'ordos-database';
    const LABEL_LORE = 'lore-edition';

    const COLLECTIONS = [
        self::LABEL_PRIME => '0xd0aaac09e7f9b794fafa9020a34ad5b906566a5c',
        self::LABEL_ORDOS => '0xd4b1a63cb167968abf039a858c3745228fff937d',
        self::LABEL_LORE => '0x495f947276749ce646f68ac8c248420045cb7b5e',
    ];

    const LORE_COLLECTION = [
        "Assa Ikeba - 'Ordo'",
        "Aren Cross - 'The Phoenix'",
        "Malady Holmes - 'The Plaguebearer'",
        "André Vidocq - 'Inspector Princeps'",
        "Ash - 'The Gorgon'",
        "Kāmaloka 'The Clairvoyant'",
        "Lison Karimov - 'Fulgur'",
        "Maëlle Ishta - 'Yogi Master'",
        "Neu Bouth - 'Bouth'",
        "Rebekha Havoc - 'Rebbi'",
        "Telmond Sabhir - 'The Artificer'",
        "The Prophet"
    ];

    const SUBTITLE_OFFSET_STR = [
        self::LABEL_PRIME => 9,
        self::LABEL_ORDOS => 1,
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

    /** @var TokenRepository */
    private $tokenRepository;

    /** @var AlchemyClient */
    private $alchemyClient;

    /**
     * @param AlchemyClient $alchemyClient
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository, AlchemyClient $alchemyClient)
    {
        $this->alchemyClient = $alchemyClient;
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
        $details = [];
        if ($identifier === self::COLLECTIONS[self::LABEL_LORE]) {
            foreach ($tokens as $token) {
                $details[$token['name']]['rewards'] = 20;
                $details[$token['name']]['img'] = $token['image'];
            }

            return [
                'sum' => count($tokens)*20,
                'details' => $details
            ];
        }

        $sum = 0;
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

    /**
     * @param string $wallet
     * @return array
     */
    public function getTokensFromWallet($wallet): array
    {
        $response = $this->alchemyClient->getNFTsCollectionsByOwner(
            array_values(self::COLLECTIONS),
            $wallet
        );
        $data = json_decode($response->getBody()->getContents(), true);
        $tokenNumberPrime = [];
        $tokenNumberOrdos = [];
        $tokenNumberLore = [];

        $contracts = array_values(self::COLLECTIONS);
        foreach ($data['ownedNfts'] as $nft) {
            if (!in_array($nft['contract']['address'], $contracts, true)) {
                continue;
            }

            if ($nft['contract']['address'] === self::COLLECTIONS[self::LABEL_ORDOS]) {
                $tokenNumberOrdos[] = substr($nft['metadata']['name'], self::SUBTITLE_OFFSET_STR[self::LABEL_ORDOS]);
                continue;
            }

            if ($nft['contract']['address'] === self::COLLECTIONS[self::LABEL_PRIME]) {
                $tokenNumberPrime[] = substr($nft['metadata']['name'], self::SUBTITLE_OFFSET_STR[self::LABEL_PRIME]);
                continue;
            }

            if (in_array($nft['metadata'], self::LORE_COLLECTION)) {
                $tokenNumberLore[] = $nft['metadata'];
            }
        }
        $primeTokens = $this->tokenRepository->findByTokenIdsAndCollection(self::COLLECTIONS[self::LABEL_PRIME], $tokenNumberPrime);
        $ordosTokens = $this->tokenRepository->findByTokenIdsAndCollection(self::COLLECTIONS[self::LABEL_ORDOS], $tokenNumberOrdos);

        return [
            'prime' => $this->process($primeTokens, self::COLLECTIONS[self::LABEL_PRIME]),
            'ordos' => $this->process($ordosTokens, self::COLLECTIONS[self::LABEL_ORDOS]),
            'lore' => $this->process($tokenNumberLore, self::COLLECTIONS[self::LABEL_LORE])
        ];
    }
}
