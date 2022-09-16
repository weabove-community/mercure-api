<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\AttributeToken;
use App\Entity\Token;
use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'snip:import')]
class SnipeImportCommand extends Command
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $start = 1;
        $end = 2;
        $metadataUrl = 'https://img.x2y2.io/ipfs/bafybeiani764y53yslrmu2lpyf7ek3idtabvlxvdjbco4sdygtezedagui/%s';
        $client = new Client();
        $attributes = [];
        for ($i = $start; $i <= $end; $i++) {
            $response = $client->request('GET', sprintf($metadataUrl, $i));
            $data = json_decode($response->getBody()->getContents(), true);
            $token = new Token();
            $token->setNumber($i);
            foreach ($data['attributes'] as $trait) {
                if (!isset($attributes[$trait['trait_type']]) ||
                    !isset($attributes[$trait['trait_type']][$trait['value']])) {

                    $attributes[$trait['trait_type']] = [];
                    $attribute = new Attribute();
                    $attribute
                        ->setType($trait['trait_type'])
                        ->setValue($trait['value'])
                    ;
                    $attributes[$trait['trait_type']][$trait['value']] = $attribute;
                }

                $attributeToken = new AttributeToken();
                $attributeToken->setToken($token);
                $attributeToken->setAttribute($attribute);
                $this->entityManager->persist($token);
                $this->entityManager->persist($attributeToken);
            }

        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}