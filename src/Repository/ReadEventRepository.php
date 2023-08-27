<?php

namespace App\Repository;

use App\Dto\SearchInput;

interface ReadEventRepository
{
    public function countAll(SearchInput $searchInput): int;

    /**
     * @return array<string, int>
     */
    public function countByType(SearchInput $searchInput): array;

    /**
     * @return array<array<string, int>>
     */
    public function statsByTypePerHour(SearchInput $searchInput): array;

    /**
     * @return array<array<string, mixed>>
     */
    public function getLatest(SearchInput $searchInput): array;

    public function exist(int $ghaId): bool;
}
