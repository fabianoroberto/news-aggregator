<?php

declare(strict_types=1);

namespace App\Dto\Item;

use JMS\Serializer\Annotation as JMS;

/**
 * This class represents an error object. It shouldn't be used directly.
 */
class ErrorItem
{
    /**
     * @JMS\Type("string")
     */
    private string $detail;

    /**
     * @JMS\Type("string")
     */
    private ?string $title = null;

    /**
     * @JMS\Type("string")
     */
    private ?string $source = null;

    public function __construct(string $detail)
    {
        $this->detail = $detail;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
