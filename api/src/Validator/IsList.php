<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Provides a shortcut constraint for arrays of single scalar type (lists).
 *
 * @psalm-suppress PropertyNotSetInConstructor Error due to extending framework base class
 */
#[\Attribute]
final class IsList extends Constraint
{
    /**
     * @param ScalarType|class-string  $type
     * @param array<array-key, string> $groups
     */
    #[HasNamedArguments]
    public function __construct(
        public ScalarType|string $type,
        public string $message = 'Array is not a valid list: {{ value }} is not of type {{ type }}.',
        array $groups = [],
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
