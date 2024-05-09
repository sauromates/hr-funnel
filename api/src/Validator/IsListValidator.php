<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validates that provided value is a list of specified type.
 *
 * @psalm-suppress UnimplementedInterfaceMethod Error due to extending framework base class
 */
final class IsListValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsList) {
            throw new UnexpectedTypeException($constraint, IsList::class);
        }

        // Custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        // see: https://symfony.com/doc/current/validation/custom_constraint.html#creating-the-validator-itself
        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        if (!$this->validateKeys($value)) {
            throw new UnexpectedValueException($value, 'list');
        }

        $errors = match ($constraint->type) {
            ScalarType::String => $this->validateString($value),
            ScalarType::Number => $this->validateNumber($value),
            ScalarType::Boolean => $this->validateBoolean($value),
            default => $this->validateObject($value, $constraint->type),
        };

        /**
         * @var scalar|object $invalidValue
         */
        foreach ($errors as $invalidValue) {
            $stringifiedType = $constraint->type instanceof ScalarType ? $constraint->type->value : $constraint->type;
            $stringifiedValue = \is_object($invalidValue) ? $invalidValue::class : (string) $invalidValue;

            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    '{{ value }}' => $stringifiedValue,
                    '{{ type }}' => $stringifiedType,
                ])
                ->setInvalidValue($invalidValue)
                ->setCause(sprintf('All values in array must be of type %s.', $stringifiedType))
                ->addViolation();
        }
    }

    /**
     * Validates that all array keys are integers (thus array is a valid list).
     *
     * @param array<mixed> $value
     */
    private function validateKeys(array $value): bool
    {
        $keys = array_keys($value);
        $stringKeys = array_filter($keys, fn (int|string $key): bool => \is_string($key));

        return 0 === \count($stringKeys);
    }

    /**
     * Returns array of values which don't pass validation.
     *
     * @param array<array-key, mixed> $value
     *
     * @return array<mixed>
     */
    private function validateString(array $value): array
    {
        return array_filter($value, fn (mixed $item): bool => !\is_string($item));
    }

    /**
     * Returns array of values which don't pass validation.
     *
     * @param array<array-key, mixed> $value
     *
     * @return array<mixed>
     */
    private function validateNumber(array $value): array
    {
        return array_filter($value, function (mixed $item): bool {
            if (\is_string($item)) {
                $item = str_replace(',', '.', $item);
            }

            return !is_numeric($item);
        });
    }

    /**
     * Returns array of values which don't pass validation.
     *
     * @param array<array-key, mixed> $value
     *
     * @return array<mixed>
     */
    private function validateBoolean(array $value): array
    {
        return array_filter($value, fn (mixed $item): bool => !\in_array($item, [true, false], strict: true));
    }

    /**
     * Returns array of values which don't pass validation.
     *
     * @param array<array-key, mixed> $value
     * @param class-string            $class
     *
     * @return array<mixed>
     */
    private function validateObject(array $value, string $class): array
    {
        return array_filter($value, fn (mixed $item): bool => !\is_object($item) && !is_a($item, $class, true));
    }
}
