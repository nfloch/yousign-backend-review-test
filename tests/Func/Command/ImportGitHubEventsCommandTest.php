<?php

namespace App\Tests\Func\Command;

use App\Entity\Event;
use App\Repository\ReadEventRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ImportGitHubEventsCommandTest extends KernelTestCase
{
    private const SOURCE_FILE_PATH = __DIR__. '/../../data/2015-01-01-1.json';
    private const DESTINATION_FILE_PATH = __DIR__.'/../../../data/2015-01-01-1.json';

    private CommandTester $commandTester;

    private Filesystem $fileSystem;

    private ReadEventRepository $readEventRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $this->fileSystem = self::getContainer()->get('filesystem');
        echo $kernel->getEnvironment();
        $this->fileSystem->copy(self::SOURCE_FILE_PATH, self::DESTINATION_FILE_PATH);

        $this->readEventRepository = self::getContainer()->get(ReadEventRepository::class);

        $command = $application->find('app:import-github-events');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->fileSystem->remove(self::DESTINATION_FILE_PATH);
        parent::tearDown();
    }

    public function testExecute(): void
    {
        $this->commandTester->execute([
            "date" => "2015-01-01",
            "hour" => "12"
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        self::assertTrue($this->readEventRepository->exist(2489418153));
    }

    /**
     * @dataProvider provideMissingArguments
     */
    public function testItThrowsExceptionWithoutAllArgs(array $args): void
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute($args);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Username: Wouter', $output);
    }

    private function provideMissingArguments(): iterable
    {
        yield "No arg" => [[]];
        yield "Date only" => [["date" => "2015-01-01"]];
        yield "Hour only" => [["hour" => "23"]];
    }

    /**
     * @dataProvider provideBadArguments
     */
    public function testItReturnsInvalidCodeWithBadArgs(array $args): void
    {
        $this->commandTester->execute($args);

        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Bad formatted arguments.', $output);
    }

    private function provideBadArguments(): iterable
    {
        yield "Bad Date" => [[
            "date" => "2015a-01-01",
            "hour" => "23"
        ]];
        yield "Bad Hour" => [[
            "date" => "2015-01-01",
            "hour" => "25"
        ]];
    }
}
