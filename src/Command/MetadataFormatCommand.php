<?php

namespace App\Command;

use App\Repository\CollectionRepository;
use App\Service\FileSystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:metadata:rename')]
class MetadataFormatCommand extends Command
{

    /** @var FileSystem */
    private $fileSystem;

    /** @var CollectionRepository */
    private $collectionRepository;

    public function __construct(FileSystem $fileSystem, CollectionRepository $collectionRepository)
    {
        $this->fileSystem = $fileSystem;
        $this->collectionRepository = $collectionRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'Smart contract/Identifier')
            ->addArgument('extension', InputArgument::OPTIONAL, 'File extension', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = $this->collectionRepository->findOneByIdentifier($input->getArgument('identifier'));
        $directory = $this->fileSystem->getMetadataDirectory($collection);
        $files = scandir($directory);

        $strlenExtension = 0;
        if ($input->getArgument('extension')) {
            $strlenExtension += strlen($input->getArgument('extension')) + 1;
        }

        $strlenMetadataFile = strlen(count($files)) + $strlenExtension;

        foreach ($files as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($input->getArgument('extension') &&
                $input->getArgument('extension') !== $ext) {
                continue;
            }

            $newFilename = str_pad($filename, $strlenMetadataFile, "0", STR_PAD_LEFT);
            $filenameOrigin = $directory.$filename;
            $filenameTarget = $directory.$newFilename;

            if(!rename($filenameOrigin, $filenameTarget)) {
                $output->writeln('Rename failed ' . $filename);
            }
        }

        return Command::SUCCESS;
    }
}