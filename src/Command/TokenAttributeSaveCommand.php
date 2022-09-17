<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\Token;
use App\Entity\TokenAttribute;
use App\Repository\AttributeRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'snip:token-save')]
class TokenAttributeSaveCommand extends Command
{
    /** @var AttributeRepository  */
    private $attributeRepository;


    public function __construct(
        ManagerRegistry $doctrine,
        AttributeRepository $attributeRepository
    )
    {
        $this->attributeRepository = $attributeRepository;
        $this->entityManager = $doctrine->getManager();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $attributes = $this->attributeRepository->findAll();
        $attributeData = [];
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $attributeData[$attribute->getTraitType()->getName()][$attribute->getValue()] = $attribute;
        }

        $directory = __DIR__ . '/../../data/WeAbove/';
        $allMetadata = [];
        $tokenId = 0;
        foreach (scandir($directory) as $filename) {
            if ($filename == '.' || $filename == '..' || $filename == '.DS_Store') {continue;}
            $json = file_get_contents($directory.$filename);
            $allMetadata[$tokenId] = json_decode($json, true);
            $tokenId++;
        }


        foreach ($allMetadata as $tokenId => $metadata) {
            $token = new Token();
            $token->setToken($tokenId);
            foreach ($metadata['attributes'] as $trait) {

                $attribute = $attributeData[$trait['trait_type']][$trait['value']];
                $tokenAttribute = new TokenAttribute();
                $tokenAttribute
                    ->setToken($token)
                    ->setAttribute($attribute);
                $this->entityManager->persist($tokenAttribute);
            }

            $this->entityManager->persist($token);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}