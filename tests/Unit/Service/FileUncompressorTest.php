<?php

namespace App\Tests\Unit\Service;

use App\Service\FileUncompressor;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FileUncompressorTest extends TestCase
{
    private FileUncompressor $testedInstance;

    protected function setUp(): void
    {
        $this->testedInstance = new FileUncompressor();
    }

    public function testUncompressFile(): void
    {
        $filePath = __DIR__ . "/../../data/test.txt.gz";
        $originalFilePath = __DIR__ . "/../../data/test.txt";
        $outputPath = sys_get_temp_dir() . "/result";

        $this->testedInstance->uncompressFile($filePath, $outputPath);

        $this->assertFileEquals($originalFilePath, $outputPath);
    }

    public function testItThrowExceptionWhenFileIsNotFound(): void
    {
        $filePath = __DIR__ . "/../../data/unknownFile.txt.gz";
        $outputPath = sys_get_temp_dir() . "/result";

        $this->expectException(RuntimeException::class);
        $this->testedInstance->uncompressFile($filePath, $outputPath);
    }
}
