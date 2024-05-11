<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Vacancy;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Vacancy;
use App\Tests\Factory\VacancyFactory;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;

final class GetVacancyItemTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    public function testCanGetVacancyItem(): void
    {
        $vacancy = VacancyFactory::createOne()->object();

        self::createAuthenticatedClient()
            ->request(Request::METHOD_GET, '/api/vacancies/'.$vacancy->getId())
            ->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/Vacancy',
            '@id' => '/api/vacancies/'.$vacancy->getId(),
            '@type' => 'Vacancy',
            'id' => $vacancy->getId(),
            'title' => $vacancy->getTitle(),
            'slug' => $vacancy->getSlug(),
            'description' => $vacancy->getDescription(),
            'shortDescription' => $vacancy->getShortDescription(),
            'requirements' => $vacancy->getRequirements(),
            'minBudget' => $vacancy->getMinBudget(),
            'maxBudget' => $vacancy->getMaxBudget(),
            'status' => $vacancy->getStatus()->value,
            'createdBy' => '/api/users/me',
            'createdAt' => $vacancy->getCreatedAt()?->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $vacancy->getUpdatedAt()?->format(\DateTimeInterface::RFC3339),
        ]);
        $this->assertMatchesResourceItemJsonSchema(Vacancy::class);
    }
}
