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
        $users = UserFactory::createMany(random_int(31, 99));
        $usersCount = \count($users) + 1; // One user is created via fixtures load
        $perPage = 30;

        $response = self::createAuthenticatedClient()
            ->request('GET', '/api/users')
            ->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => $usersCount,
            'hydra:view' => [
                '@id' => '/api/users?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/users?page=1',
                'hydra:last' => '/api/users?page='.ceil($usersCount / $perPage),
                'hydra:next' => '/api/users?page=2',
            ],
        ]);
        $this->assertCount($perPage, $response['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }
}
