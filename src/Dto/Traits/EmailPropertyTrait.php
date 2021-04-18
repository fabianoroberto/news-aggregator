<?php

declare(strict_types=1);

namespace App\Dto\Traits;

use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

trait EmailPropertyTrait
{
    /**
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\NotNull(message="Email is required.")
     * @Assert\NotBlank
     * @Assert\Email
     * @OA\Property(example="user@local.local")
     */
    private string $email;

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
