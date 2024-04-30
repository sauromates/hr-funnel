<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;

final class LoginTest extends ApiTestCase
{
    use Factories;

    public function testLogin(): void
    {
        UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'test',
        ]);

        $response = self::createClient()->request(
            method: 'POST',
            url: '/api/login_check',
            options: [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'test',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());
        $this->assertArrayHasKey('refreshToken', $response->toArray());
        $this->assertResponseHasCookie('token');
        $this->assertResponseHasCookie('refreshToken');
    }
}
