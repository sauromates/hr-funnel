<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Vacancy;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Factory\VacancyFactory;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zenstruck\Foundry\Test\Factories;

final class UpdateVacancyItemTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    private SerializerInterface&NormalizerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = self::getContainer()->get('serializer');
    }

    public function testUpdateVacancyItemWithPutRequest(): void
    {
        $vacancy = VacancyFactory::createOne();

        $vacancyData = $this->serializer->normalize($vacancy->object());
        $updates = ['title' => 'updated test title', 'shortDescription' => 'updated short description'];

        self::createAuthenticatedClient()->request(
            method: Request::METHOD_PUT,
            url: '/api/vacancies/'.$vacancy->getId(),
            options: ['json' => array_merge($vacancyData, $updates)]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($updates);

        foreach ($updates as $property => $value) {
            $getter = 'get'.ucfirst($property);
            $this->assertSame($value, $vacancy->$getter());
        }
    }

    public function testUpdateVacancyItemWithPatchRequest(): void
    {
        $vacancy = VacancyFactory::createOne();
        $updates = ['title' => 'updated test title', 'shortDescription' => 'updated short description'];

        self::createAuthenticatedClient()->request(Request::METHOD_PATCH, '/api/vacancies/'.$vacancy->getId(), [
            'json' => $updates,
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($updates);

        foreach ($updates as $property => $value) {
            $getter = 'get'.ucfirst($property);
            $this->assertSame($value, $vacancy->$getter());
        }
    }
}
