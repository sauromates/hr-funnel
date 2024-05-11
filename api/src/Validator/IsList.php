<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Provides a shortcut constraint for arrays of single scalar type (lists).
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @psalm-suppress PropertyNotSetInConstructor Error due to extending framework base class
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class IsList extends Constraint
{
    public string $message = 'Array is not a valid list: {{ value }} is not of type {{ type }}.';

    /**
     * @param ScalarType|class-string       $type
     * @param array<array-key, string>|null $groups
     */
    #[HasNamedArguments]
    public function __construct(
        public ScalarType|string $type,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
