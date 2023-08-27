<?php

namespace App\Dto;

use DateTimeImmutable;

class GHArchiveEntry
{
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly int $actorId,
        public readonly string $actorLogin,
        public readonly string $actorGravatarId,
        public readonly string $actorUrl,
        public readonly string $actorAvatarUrl,
        public readonly int $repoId,
        public readonly string $repoName,
        public readonly string $repoUrl,
        public readonly array $payload,
        public readonly bool $public,
        public readonly DateTimeImmutable $createdAt
    )
    {
    }
}
