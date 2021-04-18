<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Dto\Traits\TitlePropertyTrait;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleCreateRequest
{
    use TitlePropertyTrait;

    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="Content is required.")
     * @Assert\NotBlank
     */
    protected string $content = '';

    /**
     * @JMS\Type("string")
     * @Assert\NotNull(message="Link is required.")
     * @Assert\NotBlank
     */
    protected string $link = '';

    public function getContent(): string
    {
        return $this->content;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
