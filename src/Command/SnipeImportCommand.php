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
        $directory = __DIR__.'/../../data/WeAbove';
        $json = $this->get();
        $data = json_decode($json, true);

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


        return Command::SUCCESS;
    }

    public function get()
    {
        return '{
  "name": "WeAbove #0",
  "description": [
    "Follow the exciting story of 3 factions (Ordos, Altari, Freo) trying to survive in a world where gravity has been shaken by a powerful mineral. Please Read our Terms & Conditions at : https://weabove.io/cgv.pdf"
  ],
  "attributes": [
    {
      "trait_type": "Faction",
      "value": "Altari"
    },
    {
      "trait_type": "Skin Alteration",
      "value": "Mutation of Precognition"
    },
    {
      "trait_type": "Hair",
      "value": "White Flow"
    },
    {
      "trait_type": "Coat",
      "value": "Cursed Hood Down"
    },
    {
      "trait_type": "Eyes",
      "value": "Hazel"
    },
    {
      "trait_type": "Expression",
      "value": "Stern"
    },
    {
      "trait_type": "Earring",
      "value": "Gear Earring"
    },
    {
      "trait_type": "Background",
      "value": "off-white"
    },
    {
      "trait_type": "Crystal",
      "value": "Baetyl of Dominion"
    },
    {
      "trait_type": "Element",
      "value": "Flow lvl.1"
    }
  ],
  "image": "ipfs://bafybeidbmg442mmfymcqr6vdx5gytljp5vrybiw2nf2ez525bjbcky2ami/305.gif",
  "animation_url": "ipfs://bafybeidjwtfldmlz2mkeiq2zkinu6pbzicd5nnugjo5o7ic564xoww6w2m/305.mp4"
}';
    }
}