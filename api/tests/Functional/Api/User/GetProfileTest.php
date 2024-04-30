<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Tests\Factory\UserFactory;
use App\Tests\Util\JwtAuthenticatedClient;
use Zenstruck\Foundry\Test\Factories;

final class GetProfileTest extends ApiTestCase
{
    use Factories;
    use JwtAuthenticatedClient;

    public function testUsersCanGetTheirProfile(): void
    {
        $user = UserFactory::createOne(['password' => 'password'])->object();

        self::createAuthenticatedClient($user->getEmail(), 'password')->request('GET', '/api/users/me');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/api/users/me',
            '@type' => 'User',
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'createdAt' => $user->getCreatedAt()?->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $user->getUpdatedAt()?->format(\DateTimeInterface::RFC3339),
        ]);
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testPasswordIsVisibleOnlyInProfile(): void
    {
        // @phpstan-ignore-next-line
        $fixtureUser = UserFactory::repository()->findFixtureUser();

        $response = self::createAuthenticatedClient()
            ->request('GET', '/api/users/'.$fixtureUser->getId())
            ->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertNotContains('password', $response);
    }
}
