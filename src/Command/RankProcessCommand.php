<?php

namespace App\Command;

use App\Entity\Token;
use App\Entity\TokenAttribute;
use App\Repository\AttributeRepository;
use App\Repository\TokenAttributeRepository;
use App\Repository\TokenRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'snip:rank')]
class RankProcessCommand extends Command
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

        $tokenScore = [];
        $tokens = [];
        /** @var TokenAttribute $tokenAttribute */
        foreach ($tokenAttributes as $tokenAttribute) {
            if (!isset($tokenScore[$tokenAttribute->getToken()->getId()])) {
                $tokenScore[$tokenAttribute->getToken()->getId()] = 0;
            }
            $tokenScore[$tokenAttribute->getToken()->getId()]+= $tokenAttribute->getAttribute()->getPercent();
            $tokens[$tokenAttribute->getToken()->getId()] = $tokenAttribute->getToken();
        }

        $rank = [];
        for ($currentRank = 1; $currentRank <= 1550; $currentRank++) {
            $min = 0;
            $currentToken = null;
            foreach($tokenScore as $tokenId => $score) {
                if ($min == 0) {
                    $rank[$tokenId]['rank'] = $currentRank;
                    $rank[$tokenId]['score'] = $score;
                    $currentToken = $tokenId;
                    $min = $score;
                    continue;
                }

                if ($min > $score) {
                    $rank[$tokenId]['rank'] = $currentRank;
                    $rank[$tokenId]['score'] = $score;
                    $currentToken = $tokenId;

                    $min = $score;
                }
            }
            unset($tokenScore[$currentToken]);
        }

        foreach ($rank as $tokenId => $data) {
            $token = $tokens[$tokenId];
            $token->setScore($data['score']);
            $token->setRank($data['rank']);
            $this->entityManager->persist($token);
        }



        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}