<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Tests\Factory\UserFactory;
use App\Tests\Util\JwtAuthenticatedClient;
use Zenstruck\Foundry\Test\Factories;

final class GetUserCollectionTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    public function testCanGetUsersCollection(): void
    {
        // Create items for at least 3 page collection (hence the minimum of 91)
        UserFactory::createMany(random_int(91, 199));

        $usersCount = UserFactory::repository()->count();
        $perPage = 30;

        $response = self::createAuthenticatedClient()
            ->request('GET', '/api/users?page=2') // Page query is used to check full pagination response
            ->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => $usersCount,
            'hydra:view' => [
                '@id' => '/api/users?page=2',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/users?page=1',
                'hydra:last' => '/api/users?page='.ceil($usersCount / $perPage),
                'hydra:previous' => '/api/users?page=1',
                'hydra:next' => '/api/users?page=3',
            ],
        ]);
        $this->assertCount($perPage, $response['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }
}
