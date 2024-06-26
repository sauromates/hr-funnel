<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Util\JwtAuthenticatedClient;
use Symfony\Component\HttpFoundation\Response;

final class FirewallTest extends ApiTestCase
{
    use JwtAuthenticatedClient;

    public function testApiRoutesRequireAuthentication(): void
    {
        self::createClient()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        self::createAuthenticatedClient()->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();
    }
}
