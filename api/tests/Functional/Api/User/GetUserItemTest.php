<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Tests\Factory\UserFactory;
use App\Tests\Util\JwtAuthenticatedClient;
use Zenstruck\Foundry\Test\Factories;

final class GetUserItemTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    public function testCanGetUserItem(): void
    {
        $user = UserFactory::createOne()->object();

        self::createAuthenticatedClient()
            ->request('GET', '/api/users/'.$user->getId())
            ->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/api/users/'.$user->getId(),
            '@type' => 'User',
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'createdAt' => $user->getCreatedAt()?->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $user->getUpdatedAt()?->format(\DateTimeInterface::RFC3339),
        ]);
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }
}
