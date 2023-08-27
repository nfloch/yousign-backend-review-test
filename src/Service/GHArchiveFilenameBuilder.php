<?php

namespace App\Service;

class GHArchiveFilenameBuilder
{
    public function buildFilename(string $date, string $hour): string
    {
        return "{$date}-{$hour}.json";
    }
}
