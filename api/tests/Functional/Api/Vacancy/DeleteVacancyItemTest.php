<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Vacancy;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Factory\VacancyFactory;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;

final class DeleteVacancyItemTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    public function testDeleteVacancyItem(): void
    {
        $vacancy = VacancyFactory::createOne();

        self::createAuthenticatedClient()->request(
            method: Request::METHOD_DELETE,
            url: '/api/vacancies/'.$vacancy->getId(),
        );

        $this->assertResponseIsSuccessful();

        VacancyFactory::assert()->empty();
    }
}
