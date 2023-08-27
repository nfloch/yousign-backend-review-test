<?php

namespace App\Service;

class FileReader
{
    /**
     * @return iterable<string>
     */
    public function getIterator(string $filePath): iterable
    {
        if ($file = fopen($filePath, 'r')) {
            while (!feof($file)) {
                if (($line = fgets($file)) !== false) {
                    yield $line;
                }
            }
            fclose($file);
        }
    }
}
