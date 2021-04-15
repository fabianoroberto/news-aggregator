<?php

declare(strict_types=1);

namespace App\Repository\Common;

interface CountInterface
{
    public function getCountByFilters(array $filters = []);
}
