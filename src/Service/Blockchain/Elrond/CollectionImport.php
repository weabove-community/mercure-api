<?php

namespace App\Service\Blockchain\Elrond;

use App\ElrondApi\CollectionApi;
use App\Entity\Attribute;
use App\Entity\Collection;
use App\Entity\Rank;
use App\Entity\TokenAttribute;
use App\Entity\TraitType;
use App\Enum\BlockchainEnum;
use App\Enum\CollectionStatusEnum;
use App\Repository\AttributeRepository;
use App\Repository\CollectionRepository;
use App\Repository\RankRepository;
use App\Repository\TokenRepository;
use App\Service\AttributeService;
use App\Service\FileSystem;
use App\Service\Model\CollectionImportAbstract;
use App\Service\Model\CollectionImportInterface;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;

class CollectionImport extends CollectionImportAbstract implements CollectionImportInterface
{
    private EntityManagerInterface $em;
    private CollectionApi $collectionApi;
    private FileSystem $fileSystem;
    private CollectionRepository $collectionRepository;
    private AttributeService $attributeService;

    public function __construct(
        EntityManagerInterface $em,
        CollectionApi $collectionApi,
        FileSystem $fileSystem,
        RankRepository $rankRepository,
        AttributeService $attributeService,
        TokenService $tokenService,
        AttributeRepository $attributeRepository,
        CollectionRepository $collectionRepository,
        TokenRepository $tokenRepository
    )
    {
        $this->em = $em;
        $this->collectionApi = $collectionApi;
        $this->fileSystem = $fileSystem;
        $this->attributeService = $attributeService;
        $this->tokenService = $tokenService;
        $this->collectionRepository = $collectionRepository;
        $this->tokenRepository = $tokenRepository;
        $this->attributeRepository = $attributeRepository;
        $this->rankRepository = $rankRepository;
    }

    public function importMetadata(Collection $collection): void
    {
        $response = $this->collectionApi->count($collection->getIdentifier());
        $collection->setSupply((int) $response->getBody()->getContents());

        $queryParam = [
            'size' => 200,
            'from' => 0
        ];

        if (!$this->fileSystem->hasMetadataDirectory($collection)) {
            mkdir($this->fileSystem->getMetadataDirectory($collection));
        }
        while ($queryParam['from'] <= $collection->getSupply()) {
            $response = $this->collectionApi->getNftsCollection(
                $collection->getIdentifier(),
                $queryParam
            );

            if ($response->getStatusCode() != 200) {
                throw new \Exception('Request api "collection nft" failed');
            }

            $data = json_decode($response->getBody()->getContents(), true);
            foreach ($data as $item) {
                echo '.';
                $this->createMetadataFile($collection, $item);
            }

            $queryParam['from'] += $queryParam['size'];
            dump($queryParam['from']);
        }

        $collection->setStatus(CollectionStatusEnum::METADATA_IMPORTED->value);
        $this->collectionRepository->save($collection);
    }

    public function createMetadataFile(Collection $collection, array $data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        $filePath = $this->fileSystem->getMetadataDirectory($collection) . $data['nonce'];

        if (false === file_put_contents($filePath, $json)) {
            throw new \Exception('Cant create metadata file ' . $data['nonce']);
        }
    }

    public function saveTrait(Collection $collection): void
    {
        dump('saveTrait');
        $directory = $this->fileSystem->getMetadataDirectory($collection);
        $attributes = [];

        try {
            dump('storage trait');
            foreach (scandir($directory) as $filename) {
                if (!$this->canHandleFile($collection, $filename)) {
                    continue;
                }

                $json = file_get_contents($directory.$filename);
                $metadata = json_decode($json, true);
                foreach ($metadata['metadata']['attributes'] as $trait) {
                    if (!isset($attributes[$trait['trait_type']])) {
                        $attributes[$trait['trait_type']] = [];
                        $attributes[$trait['trait_type']][] = null;
                    }

                    if (!in_array($trait['value'], $attributes[$trait['trait_type']])) {
                        $attributes[$trait['trait_type']][] = $trait['value'];
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Sort all attributes failed');
        }
        try {
            foreach ($attributes as $strTraitType => $values) {
                $trait = new TraitType();
                $trait
                    ->setName($strTraitType)
                    ->setCollection($collection)
                ;
                foreach ($values as $value) {
                    $attribute = new Attribute();
                    $attribute
                        ->setValue($value)
                        ->setCollection($collection)
                        ->setTraitType($trait)
                    ;

                    $this->em->persist($attribute);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Save attributes failed');
        }

        $collection->setStatus(CollectionStatusEnum::TRAIT_SAVED->value);
        $this->em->persist($collection);
        $this->em->flush();
    }

    public function processTokenAttributesBinding(Collection $collection): void
    {
        dump('processTokenAttributes');

        $attributeData = $this->attributeService->getAttributesWithValue($collection);
        $nullAttributeData = $this->attributeService->getAttributesWithoutValue($collection);

        $directory = $this->fileSystem->getMetadataDirectory($collection);
        $count = 0;
        $countTokenAttributes = 0;
        foreach (scandir($directory) as $filename) {
            if (!$this->canHandleFile($collection, $filename)) {
                continue;
            }

            $json = file_get_contents($directory.$filename);
            $metadata = json_decode($json, true);
            $token = $this->tokenService->create($collection, $metadata['nonce'], $metadata['identifier'], $metadata['url']);

            $attributeKeyValue = $this->attributeService->sortMetadataAttributesByKeyValue($metadata['metadata']['attributes']);

            foreach ($nullAttributeData as $traitTypeName => $attributeNull) {

                if (isset($attributeKeyValue[$traitTypeName])) {
                    $value = $attributeKeyValue[$traitTypeName];
                    $attribute = $attributeData[$traitTypeName][$value];
                } else {
                    $attribute = $attributeNull;
                }
                $tokenAttribute = new TokenAttribute();
                $tokenAttribute->setToken($token);
                $tokenAttribute->setAttribute($attribute);

                $this->em->persist($tokenAttribute);
                if (0 == $countTokenAttributes % 500) {
                    echo '.';
                    $this->em->flush();
                }
                $countTokenAttributes++;
            }
            $count++;
        }

        $collection
            ->setStatus(CollectionStatusEnum::TOKEN_ATTRIBUTE_SAVED->value)
            ->setSupply($count)
        ;
        $this->em->flush();
    }

    public function processAttributePercent(Collection $collection): void
    {
        dump('processAttributePercent');
        $countAttributes = [];
        $attributeData = $this->attributeService->getAttributesSortTraitNameValue($collection);
        $nullAttributeData = $this->attributeService->getAttributesWithoutValue($collection);
        $directory = $this->fileSystem->getMetadataDirectory($collection);
        foreach (scandir($directory) as $filename) {
            if (!$this->canHandleFile($collection, $filename)) {
                continue;
            }
            $json = file_get_contents($directory.$filename);
            $metadata = json_decode($json, true);
            $metadataAttributesKeyValue = $this->attributeService->sortMetadataAttributesByKeyValue($metadata['metadata']['attributes']);

            foreach ($nullAttributeData as $traitTypeName => $nullAttribute) {
                if (isset($metadataAttributesKeyValue[$traitTypeName])) {
                    $val = $metadataAttributesKeyValue[$traitTypeName];
                    $attribute = $attributeData[$traitTypeName][$val];
                } else {
                    $attribute = $nullAttribute;
                }

                if (isset($countAttributes[$attribute->getId()])) {
                    $countAttributes[$attribute->getId()]++;
                    continue;
                }

                $countAttributes[$attribute->getId()] = 1;
            }
        }

        unset($attributeData);
        unset($nullAttributeData);

        $attributes = $this->attributeRepository->findBy(['collection' => $collection]);

        foreach ($attributes as $attribute) {
            if (!isset($countAttributes[$attribute->getId()])) {
                continue;
            }
            $percent = $countAttributes[$attribute->getId()]*(100/($collection->getSupply()));
            $attribute->setPercent(round($percent, 5));
            $this->em->persist($attribute);
        }

        $collection->setStatus(CollectionStatusEnum::ATTRIBUTE_PERCENT_PROCESSED->value);
        $this->em->flush();
    }

    private function processScoreCollection(Collection $collection): void
    {
        dump('process ScoreCollection');
        $offset = 0;
        $limit = 1000;
        $count = 0;
        while ($offset <= $collection->getSupply()) {
            $tokens = $this->tokenRepository->findBy(
                ['collection' => $collection],
                ['id' => 'ASC'],
                $limit,
                $offset
            );
            $offset += $limit;
            foreach ($tokens as $token) {
                $rank = $this->processScoreByToken($token);
                $this->em->persist($rank);
                if ($count % 1000 === 0) {
                    $this->em->flush();
                }
                $count++;
                echo '.';
            }
        }
        $this->em->flush();
    }

    private function processScoreByToken($token)
    {
        $sumWithoutNull = 0;
        /** @var TokenAttribute $tokenAttribute */
        foreach ($token->getTokenAttributes() as $tokenAttribute) {
            if ($tokenAttribute->getAttribute()->getValue() !== null) {
                $sumWithoutNull += $tokenAttribute->getAttribute()->getPercent();
            }
        }
        $rank = new Rank();
        $rank
            ->setToken($token)
            ->setCollection($token->getCollection())
            ->setHandoScore($sumWithoutNull)
        ;

        return $rank;
    }

    public function processRank(Collection $collection): void
    {
        $this->processScoreCollection($collection);
        dump('process rank');
        $ranking = 1;

        $ranks = $this->rankRepository->findBy(
            ['collection' => $collection],
            ['handoScore' => 'ASC']
        );

        foreach ($ranks as $rank) {
            $rank->setHandoRank($ranking);
            $this->em->persist($rank);
            if ($ranking % 500 == 0) {
                $this->em->flush();
            }
            $ranking++;
            echo '.';
        }
        $collection->setStatus(CollectionStatusEnum::RANK_EXECUTED->value);
        $this->em->flush();
    }

    public function isSupport(Collection $collection): bool
    {
        return $collection->getBlockchain() === BlockchainEnum::ELROND->value;
    }
}
