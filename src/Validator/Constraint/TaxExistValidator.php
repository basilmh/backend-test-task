<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Repository\TaxRepository;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TaxExistValidator extends ConstraintValidator
{
    private TaxRepository $taxRepository;

    public function __construct(TaxRepository $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof TaxExist) {
            throw new LogicException(sprintf('You can only pass %s constraint to this validator.', TaxExist::class));
        }

        if (null === $value || '' === $value) {
            return;
        }

        $tax = $this->taxRepository->findByTaxPrefix($value);

        if (null === $tax) {
            //            throw new Exception('some');
            $this->context->buildViolation($constraint->message)
                ->setParameter('%value%', (string) $value)
                ->addViolation();
        }
    }
}
