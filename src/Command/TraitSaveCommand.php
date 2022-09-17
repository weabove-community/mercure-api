<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\AttributeToken;
use App\Entity\TraitType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'snip:trait-save')]
class TraitSaveCommand extends Command
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = __DIR__ . '/../../data/WeAbove/';
        $allMetadata = [];
        foreach (scandir($directory) as $filename) {
            if ($filename == '.' || $filename == '..' || $filename == '.DS_Store') {continue;}
            $filepath = $directory.$filename;
            $json = file_get_contents($filepath);
            $allMetadata[] = json_decode($json, true);
        }

        $attributes = [];
        foreach ($allMetadata as $metadata) {
            foreach ($metadata['attributes'] as $trait) {
                if (!isset($attributes[$trait['trait_type']])) {
                    $attributes[$trait['trait_type']] = [];
                }

                if (!in_array($trait['value'], $attributes[$trait['trait_type']])) {
                    $attributes[$trait['trait_type']][] = $trait['value'];
                }
            }
        }

        foreach ($attributes as $strTraitType => $values) {
            $trait = new TraitType();
            $trait->setName($strTraitType);
            foreach ($values as $value) {
                $attribute = new Attribute();
                $attribute->setValue($value);
                $trait->addAttribute($attribute);
                $this->entityManager->persist($attribute);
            }
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    public function rename()
    {
        $directory = __DIR__ . '/../../data/WeAbove';
        $directoryCopy = __DIR__ . '/../../data/WeAbove';
        foreach (scandir($directory) as $filename) {
            if ($filename == '.' || $filename == '..' || $filename == '.DS_Store') {continue;}

            $newFilename = str_pad($filename, 4, "0", STR_PAD_LEFT);
            rename($directory.'/'.$filename, $directoryCopy.'/'.$newFilename);
        };
        dump(scandir($directoryCopy));exit;
    }

}