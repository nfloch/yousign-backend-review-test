<?php

declare(strict_types=1);

namespace App\Command;

use App\Dto\GHArchiveEntry;
use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\Repo;
use App\Repository\ReadActorRepository;
use App\Repository\ReadEventRepository;
use App\Repository\ReadRepoRepository;
use App\Service\FileReader;
use App\Service\FileUncompressor;
use App\Service\GHArchiveDownloader;
use App\Service\GHArchiveEventTypeMapper;
use App\Service\TemporaryFileWriter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
class ImportGitHubEventsCommand extends Command
{
    protected static $defaultName = 'app:import-github-events';

    private SymfonyStyle $io;

    private const BATCH_SIZE = 500;

    public function __construct(
        private readonly GHArchiveDownloader      $archiveDownloader,
        private readonly TemporaryFileWriter      $fileWriter,
        private readonly FileUncompressor         $fileUncompressor,
        private readonly FileReader               $fileReader,
        private readonly SerializerInterface      $serializer,
        private readonly ReadEventRepository      $readEventRepository,
        private readonly ReadActorRepository      $actorReadRepository,
        private readonly ReadRepoRepository       $repoReadRepository,
        private readonly EntityManagerInterface   $entityManager,
        private readonly Filesystem               $filesystem,
        private readonly GHArchiveEventTypeMapper $eventTypeMapper
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events. Memory leaks could happen because of doctrine even when calling clear() method. Please call with --no-debug. See https://github.com/doctrine/orm/issues/8891#issuecomment-1114855002')
            ->addArgument("date", InputArgument::REQUIRED, "The date (format YYYY-MM-DD) to fetch data from")
            ->addArgument("hour", InputArgument::REQUIRED, "The hour (1-25) to fetch data from")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $input->getArgument("date");
        $hour = $input->getArgument("hour");

        $this->io = new SymfonyStyle($input, $output);

        if (!$this->areArgumentsValid($date, $hour)) {
            $this->io->error('Bad formatted arguments.');
            return Command::INVALID;
        }

        $this->io->title("Import GitHub events for date $date and hour $hour");
        $uncompressedFilePath = $this->fetchDataToFile($date, $hour);
        $this->io->info('File downloaded and uncompressed');

        $this->io->info('Processing entries');
        $this->processBatch($uncompressedFilePath);
        $this->io->success('Data successfully processed!');

        $this->filesystem->remove($uncompressedFilePath);

        return Command::SUCCESS;
    }

    private function areArgumentsValid(string $date, string $hour): bool
    {
        return DateTimeImmutable::createFromFormat('Y-m-d', $date) !== false &&
            (is_numeric($hour) && intval($hour) >= 1 && intval($hour) <= 23);
    }

    private function fetchDataToFile(string $date, string $hour): string
    {
        $content = $this->archiveDownloader->downloadCompressed("$date-$hour.json.gz");
        $tempFilePath = $this->fileWriter->writeContentToTempFile($content, "gharchive_", ".json.gz");

        $uncompressedFilePath = str_replace('.gz', '', $tempFilePath);
        $this->fileUncompressor->uncompressFile($tempFilePath, $uncompressedFilePath);

        $this->filesystem->remove($tempFilePath);

        return $uncompressedFilePath;
    }

    private function processBatch(string $uncompressedFilePath): void
    {
        $i = 0;
        foreach ($this->io->progressIterate($this->fileReader->getIterator($uncompressedFilePath)) as $line) {
            /** @var string $line */
            $this->handleEvent($line);
            if (++$i === self::BATCH_SIZE) {
                $this->entityManager->clear();
                $i = 0;
            }
        }
    }

    private function handleEvent(string $event): void
    {
        /** @var GHArchiveEntry $ghArchiveEvent */
        $ghArchiveEvent = $this->serializer->deserialize($event, GHArchiveEntry::class, 'json');

        if (
            !$this->eventTypeMapper->isEventTypeAllowed($ghArchiveEvent->type) ||
            $this->readEventRepository->exist($ghArchiveEvent->id)
        ) {
            return;
        }

        $actor = $this->fetchOrCreateActor($ghArchiveEvent);
        $repo = $this->fetchOrCreateRepo($ghArchiveEvent);

        $event = new Event(
            null,
            $ghArchiveEvent->id,
            $this->eventTypeMapper->transformEventType($ghArchiveEvent->type),
            $actor,
            $repo,
            $ghArchiveEvent->payload,
            $ghArchiveEvent->createdAt,
            null
        );

        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    private function fetchOrCreateActor(GHArchiveEntry $ghArchiveEvent): Actor
    {
        $actor = $this->actorReadRepository->findOneBy(['ghaId' => $ghArchiveEvent->actorId]);
        if (!$actor instanceof Actor) {
            $actor = new Actor(
                null,
                $ghArchiveEvent->actorId,
                $ghArchiveEvent->actorLogin,
                $ghArchiveEvent->actorUrl,
                $ghArchiveEvent->actorAvatarUrl
            );
            $this->entityManager->persist($actor);
        }

        return $actor;
    }

    private function fetchOrCreateRepo(GHArchiveEntry $ghArchiveEvent): Repo
    {
        $repo = $this->repoReadRepository->findOneBy(['ghaId' => $ghArchiveEvent->repoId]);
        if (!$repo instanceof Repo) {
            $repo = new Repo(
                null,
                $ghArchiveEvent->repoId,
                $ghArchiveEvent->repoName,
                $ghArchiveEvent->repoUrl
            );
            $this->entityManager->persist($repo);
        }

        return $repo;
    }
}
