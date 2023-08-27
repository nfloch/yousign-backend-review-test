<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class TemporaryFileWriter
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    /**
     * @return string the path of the created temp file
     */
    public function writeContentToTempFile(string $content, string $prefix, string $suffix): string {
        $tempFilePath = $this->filesystem->tempnam(sys_get_temp_dir(), $prefix, $suffix);
        $this->filesystem->dumpFile($tempFilePath, $content);

        return $tempFilePath;
    }
}
