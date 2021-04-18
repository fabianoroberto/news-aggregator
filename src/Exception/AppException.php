<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class AppException extends \Exception
{
    /**
     * Exception Type
     */
    protected string $type;

    /**
     * Parameters and Translation parameters
     *
     * @var array
     */
    protected mixed $parameters;

    /**
     * Http Code
     */
    protected int $httpCode;

    public function __construct(string $message, array $parameters = [])
    {
        parent::__construct($message);

        $this->type = 'APP_ERROR';
        $this->httpCode = Response::HTTP_BAD_REQUEST;
        $this->parameters = $parameters;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isCritical(): bool
    {
        return false;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    protected function stripNamespaceFromClassName(string $className): string
    {
        return \mb_substr($className, \mb_strrpos($className, '\\') - \mb_strlen($className) + 1);
    }
}
