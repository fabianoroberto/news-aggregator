<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Dto\Traits\EmailPropertyTrait;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CommentCreateRequest
{
    use EmailPropertyTrait;

    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="Full name is required.")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=3,
     *     max=100,
     *     minMessage="Full name is too short. It should have 3 characters or more.",
     *     maxMessage="Full name is too long. It should have 100 characters or less."
     * )
     */
    private string $author;

    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="Content is required.")
     * @Assert\NotBlank
     */
    private string $text;

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
