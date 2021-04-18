<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Dto\Item\UserItem;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateRequest extends UserItem
{
    /**
     * @JMS\Type("string")
     * @Assert\Choice(
     *     choices={ "ROLE_ADMIN", "ROLE_USER" },
     *     message="Choose a valid role"
     * )
     * @OA\Parameter(
     *     @OA\Schema(
     *         type="string",
     *         enum={"ASC", "DESC"},
     *         default="DESC"
     *     )
     * )
     */
    protected string $role;

    public function getRole(): string
    {
        return $this->role;
    }
}
