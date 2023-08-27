<?php

namespace App\Service\DataFetcher;

use Throwable;

class GhArchiveFetcherGateway implements GhArchiveDataFetcher
{
    public function __construct(
        private readonly LocalGhArchiveFetcher $localGhArchiveFetcher,
        private readonly OnlineGhArchiveFetcher $onlineGhArchiveFetcher,
    ) {
    }

    public function fetchData(string $date, string $hour): string
    {
        try {
            return $this->localGhArchiveFetcher->fetchData($date, $hour);
        } catch (Throwable) {
        }

        try {
            return $this->onlineGhArchiveFetcher->fetchData($date, $hour);
        } catch (Throwable) {
            throw new \RuntimeException('Impossible to fetch data locally and online');
        }
    }
}
