<?php

namespace App\Service\Blockchain\ERC20;

use App\Entity\Attribute;
use App\Entity\Collection;
use App\Entity\Rank;
use App\Entity\Token;
use App\Entity\TokenAttribute;
use App\Entity\TraitType;
use App\Enum\BlockchainEnum;
use App\Enum\CollectionStatusEnum;
use App\Repository\AttributeRepository;
use App\Repository\RankRepository;
use App\Repository\TokenRepository;
use App\Service\FileSystem;
use App\Service\Model\CollectionImportInterface;
use Doctrine\ORM\EntityManagerInterface;

class CollectionImport implements CollectionImportInterface
{
    private FileSystem $fileSystem;

    private EntityManagerInterface $em;

    private AttributeRepository $attributeRepository;

    private TokenRepository $tokenRepository;

    private RankRepository $rankRepository;

    public function __construct(
        FileSystem $fileSystem,
        EntityManagerInterface $em,
        AttributeRepository $attributeRepository,
        TokenRepository $tokenRepository,
        RankRepository $rankRepository
    )
    {
        $this->em = $em;
        $this->fileSystem = $fileSystem;
        $this->attributeRepository = $attributeRepository;
        $this->tokenRepository = $tokenRepository;
        $this->rankRepository = $rankRepository;
    }

    /**
     * Useless: import metadata manually
     * solution : ipfs get [merkletree]
     */
    public function importMetadata(Collection $collection): void
    {
        $collection->setStatus(CollectionStatusEnum::METADATA_IMPORTED->value);
        $this->em->persist($collection);
        $this->em->flush();
    }

    public function saveTrait(Collection $collection): void
    {
        dump('saveTrait');
        $directory = $this->fileSystem->getMetadataDirectory($collection);

        $attributes = [];
        foreach (scandir($directory) as $filename) {
            if (!$this->canHandleFile($collection, $filename)) {
                continue;
            }

            $json = file_get_contents($directory.$filename);
            $metadata = json_decode($json, true);
            foreach ($metadata['attributes'] as $trait) {
                if (!isset($attributes[$trait['trait_type']])) {
                    $attributes[$trait['trait_type']] = [];
                    $attributes[$trait['trait_type']][] = null;
                }

                if (!in_array($trait['value'], $attributes[$trait['trait_type']])) {
                    $attributes[$trait['trait_type']][] = $trait['value'];
                }
            }
        }

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
        $collection->setStatus(CollectionStatusEnum::TRAIT_SAVED->value);
        $this->em->persist($collection);
        $this->em->flush();
    }

    public function processTokenAttributes(Collection $collection): void
    {
        dump('processTokenAttributes');
        $attributes = $this->attributeRepository->findAll();
        $attributeData = [];
        $countAttributes = [];
        $traitTypesAttributeNull = [];
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attributeData[$attribute->getTraitType()->getName()][$attribute->getValue()] = $attribute;
            $countAttributes[$attribute->getId()] = 0;
            if ($attribute->getValue() === null) {
                $traitTypesAttributeNull[$attribute->getTraitType()->getName()] = $attribute;
            }
        }

        $directory = $this->fileSystem->getMetadataDirectory($collection);
        $count = 0;
        $countTokenAttributes = 0;
        foreach (scandir($directory) as $filename) {
            if (!$this->canHandleFile($collection, $filename)) {
                continue;
            }

            $json = file_get_contents($directory.$filename);
            $metadata = json_decode($json, true);
            $tokenNumber = substr(strrchr($metadata['name'], "#"), 1);
            $token = new Token();
            $token
                ->setToken($tokenNumber)
                ->setImageUrl($metadata['image'])
                ->setCollection($collection);

            foreach ($traitTypesAttributeNull as $traitTypeName => $attributeNull) {
                $tokenAttribute = new TokenAttribute();
                $tokenAttribute->setToken($token);
                $tokenAttribute->setAttribute($attributeNull);
                foreach ($metadata['attributes'] as $trait) {
                    if ($trait['trait_type'] != $traitTypeName) {
                        continue;
                    }
                    $attribute = $attributeData[$trait['trait_type']][$trait['value']];
                    $tokenAttribute->setAttribute($attribute);
                    break;
                }

                $countAttributes[$tokenAttribute->getAttribute()->getId()]++;

                $this->em->persist($tokenAttribute);
                if (0 === $countTokenAttributes % 1000) {
                    $this->em->flush();
                }
                $countTokenAttributes++;
            }
            $count++;
        }

        foreach ($attributes as $attribute) {
            $percent = $countAttributes[$attribute->getId()]*(100/($count));
            $attribute->setPercent(round($percent, 5));
            $this->em->persist($attribute);
        }

        $this->em->flush();

        $collection
            ->setStatus(CollectionStatusEnum::TOKEN_ATTRIBUTE_SAVED->value)
            ->setSupply($count)
        ;
    }

    public function processRank(Collection $collection): void
    {
        dump('process rank');
        $offset = 0;
        $limit = 1000;

        while ($offset <= $collection->getSupply()) {
            $tokens = $this->tokenRepository->findBy(
                ['collection' => $collection],
                ['token' => 'ASC'],
                $limit,
                $offset
            );
            $offset += $limit;
            foreach ($tokens as $token) {
                $rank = $this->processScoreByToken($token);
                if (!$rank) {
                    continue;
                }
                $this->em->persist($rank);
            }
            $this->em->flush();
        }

        $collection->setStatus(CollectionStatusEnum::RANK_EXECUTED->value);
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
            ->setHandoScore($sumWithoutNull)
        ;

        return $rank;
    }

    /**
     * @param Collection $collection
     * @param string $filename
     * @return bool
     */
    private function canHandleFile(Collection $collection, $filename)
    {
        if ($filename == '.' || $filename == '..') {
            return false;
        }

        if ($collection->getTraitFileExtension() !== null &&
            pathinfo($filename, PATHINFO_EXTENSION) !== $collection->getTraitFileExtension()) {
            return false;
        }

        return true;
    }

    /**
     * @param Collection $collection
     * @return bool
     */
    public function isSupport(Collection $collection): bool
    {
        return $collection->getBlockchain() === BlockchainEnum::ERC20->value;
    }
}