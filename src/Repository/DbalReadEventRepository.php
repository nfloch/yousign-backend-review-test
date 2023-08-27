<?php

namespace App\Repository;

use App\Dto\SearchInput;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DbalReadEventRepository implements ReadEventRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function countAll(SearchInput $searchInput): int
    {
        $sql = <<<SQL
        SELECT sum(count) as count
        FROM event
        WHERE date(create_at) = :date
        AND payload like %{$searchInput->keyword}%
SQL;

        return (int) $this->connection->fetchOne($sql, [
            'date' => $searchInput->date,
        ]);
    }

    /**
     * @return array<string, int>
     *
     * @throws Exception
     */
    public function countByType(SearchInput $searchInput): array
    {
        $sql = <<<'SQL'
            SELECT type, sum(count) as count
            FROM event
            WHERE date(create_at) = :date
            AND payload like %{$searchInput->keyword}%
            GROUP BY type
SQL;

        return $this->connection->fetchAllKeyValue($sql, [
            'date' => $searchInput->date,
        ]);
    }

    /**
     * @return array<array<string, int>>
     */
    public function statsByTypePerHour(SearchInput $searchInput): array
    {
        $sql = <<<SQL
            SELECT extract(hour from create_at) as hour, type, sum(count) as count
            FROM event
            WHERE date(create_at) = :date
            AND payload like %{$searchInput->keyword}%
            GROUP BY TYPE, EXTRACT(hour from create_at)
SQL;

        $stats = $this->connection->fetchAll($sql, [
            'date' => $searchInput->date,
        ]);

        $data = array_fill(0, 24, ['commit' => 0, 'pullRequest' => 0, 'comment' => 0]);

        foreach ($stats as $stat) {
            $data[(int) $stat['hour']][$stat['type']] = $stat['count'];
        }

        return $data;
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws Exception
     */
    public function getLatest(SearchInput $searchInput): array
    {
        $sql = <<<SQL
            SELECT type, repo
            FROM event
            WHERE date(create_at) = :date
            AND payload like %{$searchInput->keyword}%
SQL;

        $result = $this->connection->fetchAllAssociative($sql, [
            'date' => $searchInput->date,
            'keyword' => $searchInput->keyword,
        ]);

        return array_map(static function ($item) {
            $item['repo'] = json_decode((string) $item['repo'], true, flags: JSON_THROW_ON_ERROR);

            return $item;
        }, $result);
    }

    public function exist(int $ghaId): bool
    {
        $sql = <<<'SQL'
            SELECT 1
            FROM event
            WHERE gha_id = :ghaId
        SQL;

        $result = $this->connection->fetchOne($sql, [
            'ghaId' => $ghaId,
        ]);

        return (bool) $result;
    }
}
