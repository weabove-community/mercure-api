<?php

namespace App\Service\Model;

use App\Entity\Collection;

abstract class CollectionImportAbstract
{
    /**
     * @param Collection $collection
     * @param string $filename
     * @return bool
     */
    protected function canHandleFile(Collection $collection, $filename)
    {
        if ($filename == '.' || $filename == '..' || $filename == '.DS_Store') {
            return false;
        }

        if ($collection->getTraitFileExtension() !== null &&
            pathinfo($filename, PATHINFO_EXTENSION) !== $collection->getTraitFileExtension()) {
            return false;
        }

        return true;
    }

    protected  function defineTokenByFilename(Collection $collection, string $filename)
    {
        if ($collection->getTraitFileExtension()) {
            $token = substr($filename, 0, strlen($collection->getTraitFileExtension()) + 1);
        } else {
            $token = $filename;
        }

        return abs((int) $token);
    }
}