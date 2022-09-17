<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\TokenAttribute;
use App\Repository\AttributeRepository;
use App\Repository\TokenAttributeRepository;
use App\Repository\TokenRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'snip:process-attribute')]
class ProcessAttributeCommand extends Command
{
    public function __construct(
        ManagerRegistry $doctrine,
        AttributeRepository $attributeRepository,
        TokenRepository $tokenRepository,
        TokenAttributeRepository $tokenAttributeRepository
    )
    {
        $this->tokenRepository = $tokenRepository;
        $this->attributeRepository = $attributeRepository;
        $this->tokenAttributeRepository = $tokenAttributeRepository;
        $this->entityManager = $doctrine->getManager();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tokenAttributes = $this->tokenAttributeRepository->findAll();
        $attributeIdsCount = [];
        /** @var TokenAttribute $tokenAttribute */
        foreach ($tokenAttributes as $tokenAttribute) {
            if (!isset($attributeIdsCount[$tokenAttribute->getAttribute()->getId()])) {
                $attributeIdsCount[$tokenAttribute->getAttribute()->getId()] = 1;
                continue;
            }

            $attributeIdsCount[$tokenAttribute->getAttribute()->getId()]++;
        }

        unset($tokenAttributes);


        $attributes = $this->attributeRepository->findAll();
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $percent = $attributeIdsCount[$attribute->getId()]*(100/1550);

            $attribute->setPercent($percent);
            $this->entityManager->persist($attribute);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
