<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class InvalidCredentialsException extends AppException
{
    public function __construct(string $message = '')
    {
        parent::__construct(empty($message) ? 'Invalid credentials!' : $message);

        $this->type = 'INVALID_CREDENTIALS';
        $this->httpCode = Response::HTTP_UNAUTHORIZED;
    }
}
