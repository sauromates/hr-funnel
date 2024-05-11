<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Validator\IsList;
use App\Validator\IsListValidator;
use App\Validator\ScalarType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<IsListValidator>
 */
final class IsListValidatorTest extends ConstraintValidatorTestCase
{
    public function testValidatorIsAssignedToConstraint(): void
    {
        $constraint = new IsList(ScalarType::String);

        $this->assertSame(IsListValidator::class, $constraint->validatedBy());
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new IsList(ScalarType::String));

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new IsList(ScalarType::String));

        $this->assertNoViolation();
    }

    /**
     * @param array<mixed>            $list
     * @param ScalarType|class-string $type
     *
     * @dataProvider listCases
     */
    public function testHomogenousListsAreValid(array $list, ScalarType|string $type): void
    {
        $this->validator->validate($list, new IsList($type));

        $this->assertNoViolation();
    }

    /**
     * @param array<mixed>            $list
     * @param ScalarType|class-string $type
     * @param array<scalar|object>    $expectedErrors
     *
     * @dataProvider invalidListsCases
     */
    public function testHeterogenousListsAraInvalid(array $list, ScalarType|string $type, array $expectedErrors): void
    {
        $constraint = new IsList($type);

        $this->validator->validate($list, $constraint);
        $violations = $this->context->getViolations();

        $this->assertCount(\count($expectedErrors), $violations);

        foreach ($violations as $key => $violation) {
            $invalidValue = $expectedErrors[$key];

            $this->assertEquals($invalidValue, $violation->getInvalidValue());

            // Don't expect actual value and type to be set in this test. Message placeholders are filled
            // by Symfony services and this test doesn't have access to the container to load them.
            $this->assertSame('Array is not a valid list: {{ value }} is not of type {{ type }}.', $violation->getMessage());
        }
    }

    public function testInvalidValuesAreStringified(): void
    {
        $constraint = new IsList(ScalarType::String);

        /** @var array<int, array{value: mixed, expectedString: string}> $input */
        $input = [
            ['value' => 1, 'expectedString' => '1'],
            ['value' => 1.2, 'expectedString' => '1.2'],
            ['value' => false, 'expectedString' => 'false'],
            ['value' => true, 'expectedString' => 'true'],
            ['value' => new \stdClass(), 'expectedString' => 'stdClass'],
            ['value' => ['test'], 'expectedString' => '["test"]'],
            ['value' => [], 'expectedString' => '[]'],
            ['value' => null, 'expectedString' => 'null'],
        ];

        $this->validator->validate(array_map(fn (array $item): mixed => $item['value'], $input), $constraint);

        $violations = $this->context->getViolations();

        foreach ($violations as $key => $violation) {
            $expectedString = $input[$key]['expectedString'];
            $expectedCause = sprintf('%s is not a valid value for a list of type string', $expectedString);

            $this->assertSame($expectedCause, $violation->getCause());
        }
    }

    public function testInvalidConstraintRaisesException(): void
    {
        $invalidConstraint = new NotBlank();
        $exception = new UnexpectedTypeException($invalidConstraint, IsList::class);

        $this->expectExceptionObject($exception);

        $this->validator->validate(null, $invalidConstraint);
    }

    /**
     * @dataProvider invalidValuesCases
     */
    public function testOnlyArraysCanBeValidated(mixed $value): void
    {
        $this->expectExceptionObject(new UnexpectedValueException($value, 'array'));
        $this->validator->validate($value, new IsList(ScalarType::String));
    }

    public function testAssociativeArraysAreForbidden(): void
    {
        $associativeArray = ['hash' => 'map'];
        $this->expectExceptionObject(new UnexpectedValueException($associativeArray, 'list'));

        $this->validator->validate($associativeArray, new IsList(ScalarType::String));
    }

    /**
     * @return \Generator<array{0: array<int, scalar|object>, 1: ScalarType|class-string}>
     */
    public function listCases(): \Generator
    {
        yield 'a list of strings' => [['test 1', 'test 2'], ScalarType::String];
        yield 'a list of numbers' => [[1, 2, 4.5, '0,55'], ScalarType::Number];
        yield 'a list of booleans' => [[true, true, false], ScalarType::Boolean];
        yield 'a list of objects' => [[new \stdClass(), new \stdClass()], \stdClass::class];
    }

    /**
     * @return \Generator<array{0: array<scalar|object>, 1: ScalarType|class-string, 2: array<scalar|object>}>
     */
    public function invalidListsCases(): \Generator
    {
        $mixedValues = ['test', 1, false, new \stdClass()];

        yield 'an array of mixed values against string constraint' => [
            $mixedValues,
            ScalarType::String,
            [1, false, new \stdClass()],
        ];

        yield 'an array of mixed values against number constraint' => [
            $mixedValues,
            ScalarType::Number,
            ['test', false, new \stdClass()],
        ];

        yield 'an array of mixed values against boolean constraint' => [
            $mixedValues,
            ScalarType::Boolean,
            ['test', 1, new \stdClass()],
        ];

        yield 'an array of mixed values against class constraint' => [
            $mixedValues,
            \stdClass::class,
            ['test', 1, false],
        ];
    }

    /**
     * Generates test cases for every possible value that is not an array.
     *
     * @return \Generator<array<scalar|object>>
     */
    public function invalidValuesCases(): \Generator
    {
        $invalid = [
            'integer test case' => 1,
            'float test case' => 1.2,
            'string test case' => 'test',
            'boolean test case' => true,
            'object test case' => new \stdClass(),
        ];

        foreach ($invalid as $dataset => $case) {
            yield $dataset => [$case];
        }
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new IsListValidator();
    }
}
