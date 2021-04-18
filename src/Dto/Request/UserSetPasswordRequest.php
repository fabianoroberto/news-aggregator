<?php

declare(strict_types=1);

namespace App\Dto\Request;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class UserSetPasswordRequest
{
    /**
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(min=4)
     */
    protected string $newPassword;

    /**
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected string $token;

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
