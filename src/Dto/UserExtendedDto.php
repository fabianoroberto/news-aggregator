<?php

declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class UserExtendedDto extends UserDto
{
    /**
     * @var bool
     * @JMS\Type("boolean")
     * @Assert\Type("boolean")
     * @Assert\NotNull
     */
    protected $enabled;

    /**
     * @var string
     * @JMS\Type("string")
     * @Assert\Choice(
     *     choices={ "ROLE_ADMIN", "ROLE_USER" },
     *     message="Choose a valid role"
     * )
     */
    protected $role;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
