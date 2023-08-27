<?php

namespace App\Service\DataFetcher;

use RuntimeException;

interface GhArchiveDataFetcher
{
    const DATA_DIR = __DIR__ . '/../../../data/';

    /**
     * @param string $date
     * @param string $hour
     * @return string The path to the json file containing the data
     * @throws RuntimeException
     */
    public function fetchData(string $date, string $hour): string;
}
