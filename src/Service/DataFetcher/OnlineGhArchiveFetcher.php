<?php

namespace App\Service\DataFetcher;

use App\Service\FileUncompressor;
use App\Service\GHArchiveDownloader;
use App\Service\GHArchiveFilenameBuilder;
use App\Service\TemporaryFileWriter;
use Symfony\Component\Filesystem\Filesystem;

class OnlineGhArchiveFetcher implements GhArchiveDataFetcher
{
    public function __construct(
        private readonly GHArchiveFilenameBuilder $filenameBuilder,
        private readonly GHArchiveDownloader $archiveDownloader,
        private readonly TemporaryFileWriter $fileWriter,
        private readonly FileUncompressor $fileUncompressor,
        private readonly Filesystem $filesystem,
    )
    {
    }

    public function fetchData(string $date, string $hour): string
    {
        $dataFileName = $this->filenameBuilder->buildFilename($date, $hour);
        $content = $this->archiveDownloader->downloadCompressed($dataFileName . ".gz");
        $tempFilePath = $this->fileWriter->writeContentToTempFile($content, "gharchive_", ".json.gz");

        $dataFilePath = self::DATA_DIR . $dataFileName;
        $this->fileUncompressor->uncompressFile($tempFilePath, $dataFilePath);

        $this->filesystem->remove($tempFilePath);

        return $dataFilePath;
    }
}
