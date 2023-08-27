<?php

namespace App\Tests\Unit\Service;

use App\Service\GHArchiveDownloader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @internal
 *
 * @coversNothing
 */
class GHArchiveDownloaderTest extends TestCase
{
    public function testDownloadCompressed(): void
    {
        $client = new MockHttpClient([
            new MockResponse($data = uniqid('data', true)),
        ]);

        $testedInstance = new GHArchiveDownloader($client);

        $content = $testedInstance->downloadCompressed(uniqid('file_', true));
        self::assertEquals($data, $content);
    }

    public function testDownloadWhenExceptionOccurs(): void
    {
        $client = new MockHttpClient([
            new MockResponse([new \RuntimeException('Error at transport level')]),
        ]);

        $testedInstance = new GHArchiveDownloader($client);

        $this->expectException(\RuntimeException::class);
        $testedInstance->downloadCompressed(uniqid('file_', true));
    }

    public function testDownloadWithBadResponseCode(): void
    {
        $client = new MockHttpClient([
            new MockResponse('', ['http_code' => 404]),
        ]);

        $testedInstance = new GHArchiveDownloader($client);

        $this->expectException(\RuntimeException::class);
        $testedInstance->downloadCompressed(uniqid('file_', true));
    }
}
