<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use App\Entity\User;
use App\Tests\Factory\UserFactory;
use App\Tests\Functional\Api\ApiCollectionTestCase;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ApiCollectionTestCase<User>
 */
final class GetUserCollectionTest extends ApiCollectionTestCase
{
    use JwtAuthenticatedClient;

    public function testCanGetUsersCollection(): void
    {
        self::createAuthenticatedClient()->request(Request::METHOD_GET, $this->getCollectionEndpoint());

        $this->assertCollectionResponseIsSuccessfull();
    }

    protected static function getResource(): string
    {
        return User::class;
    }

    protected static function getPath(): string
    {
        return '/api/users';
    }

    protected static function getFactory(): string
    {
        return UserFactory::class;
    }
}
