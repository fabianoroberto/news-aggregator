<?php

declare(strict_types=1);

namespace App\Controller\Rest\Traits;

use App\Dto\Item\ErrorItem;
use App\Dto\Response\ErrorResponse;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ErrorResponseTrait
{
    public function constraintViolationView(ConstraintViolationListInterface $errors): View
    {
        $data = new ErrorResponse();

        /** @var ConstraintViolation $ve */
        foreach ($errors as $ve) {
            $e = new ErrorItem($ve->getMessage());
            $e->setSource($ve->getPropertyPath());
            $e->setTitle('Invalid data');
            $data->add($e);
        }

        return View::create($data, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
