<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GHArchiveDownloader
{
    private const BASE_URL = 'https://data.gharchive.org/';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @param string $fileName Compressed file to download
     *
     * @return string The content of the compressed file as a string
     *
     * @throws \RuntimeException
     */
    public function downloadCompressed(string $fileName): string
    {
        try {
            $response = $this->httpClient->request('GET', self::BASE_URL.$fileName);
            if (($statusCode = $response->getStatusCode()) !== Response::HTTP_OK) {
                throw new \RuntimeException('Bad response code from gharchive', $statusCode);
            }

            return $response->getContent();
        } catch (\Throwable $throwable) {
            throw new \RuntimeException('Error when fetching gharchive compressed data', previous: $throwable);
        }
    }
}
