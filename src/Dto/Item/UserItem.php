<?php

declare(strict_types=1);

namespace App\Dto\Item;

use App\Dto\Traits\EmailPropertyTrait;
use App\Dto\Traits\FullNamePropertiesTrait;

class UserItem
{
    use EmailPropertyTrait;
    use FullNamePropertiesTrait;
}