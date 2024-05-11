<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Vacancy;

use App\Entity\Vacancy;
use App\Tests\Factory\VacancyFactory;
use App\Tests\Functional\Api\ApiCollectionTestCase;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ApiCollectionTestCase<Vacancy>
 */
final class GetVacancyCollectionTest extends ApiCollectionTestCase
{
    use JwtAuthenticatedClient;

    public function testGetVacanciesCollection(): void
    {
        self::createAuthenticatedClient()->request(Request::METHOD_GET, $this->getCollectionEndpoint());

        $this->assertCollectionResponseIsSuccessfull();
    }

    protected static function getPath(): string
    {
        return '/api/vacancies';
    }

    protected static function getResource(): string
    {
        return Vacancy::class;
    }

    protected static function getFactory(): string
    {
        return VacancyFactory::class;
    }
}
