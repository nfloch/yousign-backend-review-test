<?php

namespace App\Tests\Unit\Service;

use App\Service\FileReader;
use PHPUnit\Framework\TestCase;

class FileReaderTest extends TestCase
{
    private FileReader $testedInstance;

    protected function setUp(): void
    {
        $this->testedInstance = new FileReader();
    }

    public function testGetIterator(): void
    {
        $filePath = __DIR__.'/../../data/test.txt';
        $lines = [...$this->testedInstance->getIterator($filePath)];
        $this->assertEquals("azerty\n", $lines[0]);
        $this->assertEquals("123456\n", $lines[1]);
        $this->assertCount(2, $lines);
    }
}
