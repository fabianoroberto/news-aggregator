<?php

declare(strict_types=1);

namespace App\Repository\Common;

use Pagerfanta\Pagerfanta;

interface PaginatorInterface
{
    public function getPaginatorByFilters(
        array $filters,
        array $orderBy = null,
        int $page = 1,
        int $limit = 10
    ): Pagerfanta;
}
