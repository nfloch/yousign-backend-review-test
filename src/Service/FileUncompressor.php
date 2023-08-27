<?php

namespace App\Service;

use RuntimeException;
use Throwable;

class FileUncompressor
{
    public function __construct(
        private readonly int $bufferSize = 4096,
    )
    {
    }

    /**
     * Uncompress a file into the specified output path
     * @throws RuntimeException
     */
    public function uncompressFile(string $compressedFiledPath, string $outputPath): void {
        try {
            $out_file = fopen($outputPath, 'wb');
            $file = gzopen($compressedFiledPath, 'rb');

            while (!gzeof($file)) {
                fwrite($out_file, gzread($file, $this->bufferSize));
            }

            fclose($out_file);
            gzclose($file);
        } catch (Throwable $throwable) {
            throw new RuntimeException("An error occurred when uncompressing file", previous: $throwable);
        }

    }
}
