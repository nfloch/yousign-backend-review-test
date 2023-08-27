<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`event`",
 *      indexes={@ORM\Index(name="IDX_EVENT_TYPE", columns={"type"})},
 *      uniqueConstraints={
 *           @ORM\UniqueConstraint(name="event_gha_id", fields={"ghaId"})
 *      }
 * )
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private int $id;

    /**
     * @ORM\Column(type="EventType", nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $count = 1;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        /**
         * @ORM\Column(type="bigint")
         */
        public int $ghaId,
        string $type,

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\Actor", cascade={"persist"})
         * @ORM\JoinColumn(name="actor_id", referencedColumnName="id")
         */
        private Actor $actor,

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\Repo", cascade={"persist"})
         * @ORM\JoinColumn(name="repo_id", referencedColumnName="id")
         */
        private Repo $repo,

        /**
         * @ORM\Column(type="json", nullable=false, options={"jsonb"=true})
         */
        private array $payload,

        /**
         * @ORM\Column(type="datetime_immutable", nullable=false)
         */
        private \DateTimeImmutable $createAt,

        /**
         * @ORM\Column(type="text", nullable=true)
         */
        private ?string $comment,
    ) {
        EventType::assertValidChoice($type);
        $this->type = $type;

        if (EventType::COMMIT === $type) {
            $this->count = $payload['size'] ?? 1;
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function actor(): Actor
    {
        return $this->actor;
    }

    public function repo(): Repo
    {
        return $this->repo;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    public function createAt(): \DateTimeImmutable
    {
        return $this->createAt;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
