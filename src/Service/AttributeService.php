<?php

namespace App\Service;

use App\Entity\Collection;
use App\Repository\AttributeRepository;

class AttributeService
{
    private AttributeRepository $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param array $attributeFromMetadata
     * @return array
     */
    public function sortMetadataAttributesByKeyValue(array $attributeFromMetadata)
    {
        $result = [];
        foreach ($attributeFromMetadata as $trait) {
            $result[$trait['trait_type']] = $trait['value'];
        }

        return $result;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public function getAttributesWithValue(Collection $collection)
    {
        $attributes = $this->attributeRepository->getByCollection($collection, true);
        $attributeData = [];
        foreach ($attributes as $attribute) {
            $attributeData[$attribute->getTraitType()->getName()][$attribute->getValue()] = $attribute;
        }

        return $attributeData;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public function getAttributesWithoutValue(Collection $collection)
    {
        $nullAttributes = $this->attributeRepository->getByCollection($collection, false);

        $attributeData = [];
        foreach ($nullAttributes as $attribute) {
            $attributeData[$attribute->getTraitType()->getName()] = $attribute;
        }

        return $attributeData;
    }

    public function getAttributesSortTraitNameValue(Collection $collection)
    {
        $attributes = $this->attributeRepository->findBy(
            ['collection' => $collection],
            ['id' => 'ASC']);
        foreach ($attributes as $attribute) {
            $attributeData[$attribute->getTraitType()->getName()][$attribute->getValue()] = $attribute;
        }

        return $attributeData;
    }

}