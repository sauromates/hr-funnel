<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Vacancy;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;

final class CreateVacancyItemTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    public function testCreateVacancyItem(): void
    {
        $requestBody = [
            'title' => 'Test without slug',
            'shortDescription' => 'Short test',
            'description' => 'Long test',
            'requirements' => ['test 1', 'test 2'],
            'minBudget' => 100,
            'maxBudget' => 200,
            'status' => 'draft',
        ];

        $response = self::createAuthenticatedClient()
            ->request(Request::METHOD_POST, '/api/vacancies', ['json' => $requestBody])
            ->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => $requestBody['title'],
            'shortDescription' => $requestBody['shortDescription'],
            'description' => $requestBody['description'],
            'requirements' => $requestBody['requirements'],
            'minBudget' => $requestBody['minBudget'],
            'maxBudget' => $requestBody['maxBudget'],
            'status' => $requestBody['status'],
            'createdBy' => '/api/users/me',
            'slug' => 'test-without-slug',
        ]);

        $this->assertNotEmpty($response['createdAt']);
        $this->assertNotEmpty($response['updatedAt']);
    }

    /**
     * @dataProvider invalidRequestBodyCases
     *
     * @param array<string, scalar>                                    $requestBody
     * @param array<int, array{propertyPath: string, message: string}> $expectedViolations
     */
    public function testValidateCreateVacancyRequest(array $requestBody, array $expectedViolations): void
    {
        $response = self::createAuthenticatedClient()->request(
            method: Request::METHOD_POST,
            url: '/api/vacancies',
            options: ['json' => $requestBody]
        );

        $this->assertResponseIsUnprocessable();
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');

        $errorContent = json_decode($response->getContent(throw: false), associative: true);
        $this->assertArraySubset([
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'title' => 'An error occurred',
        ], $errorContent);

        $violations = $this->stripViolations($errorContent['violations']);

        $this->assertCount(\count($expectedViolations), $violations);
        $this->assertSame($expectedViolations, $violations);
    }

    /**
     * @return \Generator<array{0: array<string, mixed>, 1: array<int, array{propertyPath: string, message: string}>}>
     */
    public static function invalidRequestBodyCases(): \Generator
    {
        yield 'missing required fields' => [
            [],
            [
                ['propertyPath' => 'title', 'message' => 'This value should not be blank.'],
                ['propertyPath' => 'minBudget', 'message' => 'This value should not be blank.'],
            ],
        ];

        yield 'too long strings' => [
            ['title' => str_repeat('t', 256), 'shortDescription' => str_repeat('t', 256), 'minBudget' => 1],
            [
                ['propertyPath' => 'title', 'message' => 'This value is too long. It should have 255 characters or less.'],
                ['propertyPath' => 'shortDescription', 'message' => 'This value is too long. It should have 255 characters or less.'],
            ],
        ];

        yield 'non-integer minimum budget' => [
            ['title' => 'test', 'minBudget' => -1.2],
            [['propertyPath' => 'minBudget', 'message' => 'This value should be of type int.']],
        ];

        yield 'negative minimum budget' => [
            ['title' => 'test', 'minBudget' => -1],
            [['propertyPath' => 'minBudget', 'message' => 'This value should be positive.']],
        ];

        yield 'non-integer maximum budget' => [
            ['title' => 'test', 'minBudget' => 1, 'maxBudget' => 1.5],
            [['propertyPath' => 'maxBudget', 'message' => 'This value should be of type int.']],
        ];

        yield 'maximum budget lesser than minimum' => [
            ['title' => 'test', 'minBudget' => 5, 'maxBudget' => 4],
            [['propertyPath' => 'maxBudget', 'message' => 'This value should be greater than or equal minBudget (5).']],
        ];

        yield 'non-array requirements field' => [
            ['title' => 'test', 'minBudget' => 1, 'requirements' => 1],
            [['propertyPath' => 'requirements', 'message' => 'This value should be of type array.']],
        ];

        yield 'mixed requirements list' => [
            ['title' => 'test', 'minBudget' => 1, 'requirements' => [1, 1.2, 'test', false, ['test'], null]],
            [
                ['propertyPath' => 'requirements', 'message' => 'Array is not a valid list: 1 is not of type string.'],
                ['propertyPath' => 'requirements', 'message' => 'Array is not a valid list: 1.2 is not of type string.'],
                ['propertyPath' => 'requirements', 'message' => 'Array is not a valid list: false is not of type string.'],
                ['propertyPath' => 'requirements', 'message' => 'Array is not a valid list: ["test"] is not of type string.'],
                ['propertyPath' => 'requirements', 'message' => 'Array is not a valid list: null is not of type string.'],
            ],
        ];
    }

    /**
     * Removes unwanted elements from violations.
     *
     * @param array<int, array<string, scalar>> $violations Raw violations array to process
     * @param non-empty-list<string>            $keys       List of keys to keep in each violation
     *
     * @return array<int, array<string, scalar>>
     */
    private function stripViolations(array $violations, array $keys = ['propertyPath', 'message']): array
    {
        return array_map(function (array $violation) use ($keys): array {
            foreach ($violation as $key => $value) {
                if (!\in_array($key, $keys)) {
                    unset($violation[$key]);
                }
            }

            return $violation;
        }, $violations);
    }
}
