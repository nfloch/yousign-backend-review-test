<?php

namespace App\Service\DataFetcher;

interface GhArchiveDataFetcher
{
    public const DATA_DIR = __DIR__.'/../../../data/';

    /**
     * @return string The path to the json file containing the data
     *
     * @throws \RuntimeException
     */
    public function fetchData(string $date, string $hour): string;
}
