<?php

declare(strict_types=1);

namespace App\Dto\Request;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdatePasswordRequest
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
    protected string $oldPassword;

    /**
     * @Assert\IsTrue(message="New Password must be different from old one")
     */
    public function isNewPasswordDifferent(): bool
    {
        return $this->newPassword !== $this->oldPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }
}
