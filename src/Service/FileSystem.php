<?php

namespace App\Service;

use App\Entity\Collection;
use Symfony\Component\HttpKernel\KernelInterface;

class FileSystem
{
    /** @var string  */
    private $projectDirectory;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDirectory = $kernel->getProjectDir();
    }

    /**
     * @return string
     */
    public function getProjectDirectory(): string
    {
        return $this->projectDirectory;
    }

    /**
     * @return string
     */
    public function getDataDirectory(): string
    {
        return $this->getProjectDirectory() . '/data' ;
    }

    /**
     * @param Collection $collection
     * @return string
     */
    public function getMetadataDirectory(Collection $collection): string
    {
        return sprintf(
            '%s/metadata/%s/%s',
            $this->getDataDirectory(),
            $collection->getBlockchain(),
            $collection->getIdentifier()
        );
    }
}
