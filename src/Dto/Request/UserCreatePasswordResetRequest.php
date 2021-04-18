<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Dto\Traits\EmailPropertyTrait;

class UserCreatePasswordResetRequest
{
    use EmailPropertyTrait;
}
