<?php

declare(strict_types=1);

namespace App\Dto\Response;

use App\Dto\Item\ErrorItem;
use JMS\Serializer\Annotation as JMS;

/**
 * Class ErrorResponse.
 *
 * This class represents the error response according
 * to JSON API standard https://jsonapi.org/format/#error-objects
 *
 * All JSON errors (including validation) must be
 * encapsulated in an instance of this class.
 *
 * You can make helper/builder/assembler/utility class to
 * map your error or exception to an instance of this class.
 */
class ErrorResponse
{
    /**
     * @var ErrorItem[]
     * @JMS\Type("array")
     */
    private array $errors;

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    public function add(ErrorItem $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return ErrorItem[]
     */
    public function all(): array
    {
        return $this->errors;
    }
}
