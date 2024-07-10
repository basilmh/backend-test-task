<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;
use function property_exists;

abstract class BaseRequest
{
    private ValidatorInterface $validator;
    private RequestStack $requestStack;

    public function __construct(ValidatorInterface $validator, RequestStack $requestStack)
    {
        $this->validator = $validator;
        $this->requestStack = $requestStack;
        $this->populate();
    }

    public function validate(): array
    {
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validate($this);

        if (count($errors) > 0) {
            $messages = ['message' => 'validation_failed', 'errors' => []];

            foreach ($errors as $message) {
                $messages['errors'][] = [
                    'value' => $message->getInvalidValue(),
                    'message' => $message->getMessage(),
                ];
            }

            return $messages;
        }

        return [];
    }

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function populate(): void
    {
        foreach ($this->getRequest()->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }
}
