<?php

namespace App\Service\DataFetcher;

use App\Service\GHArchiveFilenameBuilder;
use Symfony\Component\Filesystem\Filesystem;

class LocalGhArchiveFetcher implements GhArchiveDataFetcher
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly GHArchiveFilenameBuilder $filenameBuilder,
    ) {
    }

    public function fetchData(string $date, string $hour): string
    {
        $dataFilePath = self::DATA_DIR.$this->filenameBuilder->buildFilename($date, $hour);

        if (!$this->filesystem->exists($dataFilePath)) {
            throw new \RuntimeException('local data file not found on system');
        }

        return $dataFilePath;
    }
}
