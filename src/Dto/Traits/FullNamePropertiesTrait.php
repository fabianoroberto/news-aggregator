<?php

declare(strict_types=1);

namespace App\Dto\Traits;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

trait FullNamePropertiesTrait
{
    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="First name is required.")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=3,
     *     max=100,
     *     minMessage="First name is too short. It should have 3 characters or more.",
     *     maxMessage="First name is too long. It should have 100 characters or less."
     * )
     */
    private string $firstName;

    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="Last name is required.")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=2,
     *     max=100,
     *     minMessage="Last name is too short. It should have 2 characters or more.",
     *     maxMessage="Last name is too long. It should have 100 characters or less."
     * )
     */
    private string $lastName;

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
