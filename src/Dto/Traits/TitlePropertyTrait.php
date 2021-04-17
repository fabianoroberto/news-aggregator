<?php

declare(strict_types=1);

namespace App\Dto\Traits;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

trait TitlePropertyTrait
{
    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="Title is required.")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=4,
     *     max=255,
     *     minMessage="Title is too short. It should have 4 characters or more.",
     *     maxMessage="Title is too long. It should have 255 characters or less."
     * )
     */
    private string $title;

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
