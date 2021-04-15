<?php

declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto extends ValidatedDto
{
    /**
     * @var string
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\NotNull(message="Email is required.")
     * @Assert\NotBlank
     * @Assert\Email
     * @SWG\Property(example="user@local.local")
     */
    protected $email;

    /**
     * @var string
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\NotNull
     * @Assert\Length(
     *     max=100,
     *     maxMessage="Max lenght is 100"
     * )
     */
    protected $firstName;

    /**
     * @var string
     * @JMS\Type("string")
     * @Assert\Type("string")
     * @Assert\NotNull
     * @Assert\Length(
     *     max=100,
     *     maxMessage="Max lenght is 100"
     * )
     */
    protected $lastName;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
