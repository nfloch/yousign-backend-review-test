<?php

namespace App\Serializer;

use App\Dto\GHArchiveEntry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class GHArchiveEventDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): GHArchiveEntry
    {
        $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $data['created_at']);
        if ($createdAt === false) {
            throw new \RuntimeException('Unable to convert date string to DateTimeInterface object');
        }
        return new GHArchiveEntry(
            $data['id'],
            $data['type'],
            $data['actor']['id'],
            $data['actor']['login'],
            $data['actor']['gravatar_id'],
            $data['actor']['url'],
            $data['actor']['avatar_url'],
            $data['repo']['id'],
            $data['repo']['name'],
            $data['repo']['url'],
            $data['payload'],
            $data['public'],
            $createdAt,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return GHArchiveEntry::class === $type;
    }
}
