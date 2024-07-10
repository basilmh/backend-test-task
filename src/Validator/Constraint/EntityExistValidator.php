<?php
/**
 * package not exist for php 8+ so it's included as is.
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Radoje Albijanic <radoje@blackmountainlabs.me>
 */
declare(strict_types=1);

namespace App\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function sprintf;

final class EntityExistValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExist) {
            throw new LogicException(sprintf('You can only pass %s constraint to this validator.', EntityExist::class));
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (empty($constraint->entity)) {
            throw new LogicException(sprintf('Must set "entity" on "%s" validator', EntityExist::class));
        }

        $data = $this->entityManager->getRepository($constraint->entity)->findOneBy([
            $constraint->property => $value,
        ]);

        if (null === $data) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%entity%', $constraint->entity)
                ->setParameter('%property%', $constraint->property)
                ->setParameter('%value%', (string) $value)
                ->addViolation();
        }
    }
}
