<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      name="repo",
 *      uniqueConstraints={
 *            @ORM\UniqueConstraint(name="repo_gha_id", fields={"ghaId"})
 *      }
 * )
 */
class Repo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private ?int $id;

    /**
     * @ORM\Column(type="bigint")
     */
    public int $ghaId;

    /**
     * @ORM\Column(type="string")
     */
    public string $name;

    /**
     * @ORM\Column(type="string")
     */
    public string $url;

    public function __construct(?int $id, int $ghaId, string $name, string $url)
    {
        $this->id = $id;
        $this->ghaId = $ghaId;
        $this->name = $name;
        $this->url = $url;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): string
    {
        return $this->url;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['name'],
            $data['url']
        );
    }
}
