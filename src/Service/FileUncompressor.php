<?php

namespace App\Service;

class FileUncompressor
{
    public function __construct(
        private readonly int $bufferSize = 4096,
    ) {
    }

    /**
     * Uncompress a file into the specified output path.
     *
     * @throws \RuntimeException
     */
    public function uncompressFile(string $compressedFiledPath, string $outputPath): void
    {
        try {
            $out_file = fopen($outputPath, 'wb');
            $file = gzopen($compressedFiledPath, 'rb');

            if (false === $out_file || false === $file) {
                throw new \RuntimeException('An error occurred when opening stream for compressed file or destination file');
            }

            while (!gzeof($file)) {
                if (($readBuffer = gzread($file, $this->bufferSize)) !== false) {
                    fwrite($out_file, $readBuffer);
                }
            }

            fclose($out_file);
            gzclose($file);
        } catch (\Throwable $throwable) {
            throw new \RuntimeException('An error occurred when uncompressing file', previous: $throwable);
        }
    }
}
