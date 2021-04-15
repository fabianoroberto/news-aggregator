<?php

/** @noinspection PhpUndefinedClassInspection */

declare(strict_types=1);

namespace App\Dto;

use App\Exception\ValidationException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Throwable;

class ValidatedDto
{
    public function __construct(array $data)
    {
        $errorList = new ConstraintViolationList();
        $className = static::class;

        // Init Extractor library
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        $propertyInfoExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
        );

        // Check that all fields exist
        /* @phpstan-ignore-next-line */
        foreach ($this as $key => $value) {
            if (!\array_key_exists($key, $data)) {
                // throw new Exception("Field $key is missing!");
                $errorList->add(new ConstraintViolation('Value is missing', null, [], null, $key, null));
            }
        }

        // Loadd all input data
        foreach ($data as $key => $value) {
            if (\property_exists($this, $key)) {
                $propertyTypes = $propertyInfoExtractor->getTypes($className, $key);
                if (\count($propertyTypes) > 0) {
                    $propertyType = $propertyTypes[0];
                    if ($propertyType->getBuiltinType() === 'object' && $value !== null) {
                        $propertyClass = $propertyType->getClassName();
                        if (!empty($propertyClass)) {
                            try {
                                $this->{$key} = new $propertyClass($value);
                            } catch (Throwable $e) {
                                $message = "This value cannot be used to call the constructor of {$propertyClass}";
                                $errorList->add(new ConstraintViolation($message, null, [], $value, $key, $value));
                            }
                        }
                    } elseif ($propertyType->isCollection()) {
                        if (!\is_array($value)) {
                            $message = 'This value must be an array';
                            $errorList->add(new ConstraintViolation($message, null, [], $value, $key, $value));
                        } else {
                            $items = $value;
                            $itemType = $propertyType->getCollectionValueType();
                            $itemClass = $itemType->getClassName();
                            $validatedItems = [];
                            foreach ($items as $item) {
                                if ($itemType->getBuiltinType() === 'object' && $itemClass !== null) {
                                    $validatedItems[] = new $itemClass($item);
                                } else {
                                    $validatedItems[] = $item;
                                }
                            }
                            $this->{$key} = $validatedItems;
                        }
                    } else {
                        $this->{$key} = $value;
                    }
                }
            }
        }

        // Validate
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $validationErrors = $validator->validate($this);
        $errorList->addAll($validationErrors);

        if (\count($errorList) > 0) {
            throw new ValidationException(static::class, $data, $errorList);
        }
    }
}
