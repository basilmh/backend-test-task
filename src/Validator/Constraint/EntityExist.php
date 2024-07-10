<?php
/**
 * package not exist for php 8+ so it's included as is.
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Radoje Albijanic <radoje@blackmountainlabs.me>
 */
declare(strict_types=1);

namespace App\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class EntityExist extends Constraint
{
    public string $message = 'Entity "%entity%" with property "%property%": "%value%" does not exist.';
    public string $property = 'id';
    public mixed $entity;

    public function __construct(
        mixed $entity = null,
        ?string $property = null,
        ?string $message = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->entity = $entity ?? $this->entity;
        $this->property = $property ?? $this->property;
        $this->message = $message ?? $this->message;
    }
}
