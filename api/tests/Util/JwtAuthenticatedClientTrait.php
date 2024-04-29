<?php

declare(strict_types=1);

namespace App\Tests\Util;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\DataFixtures\UserFixtures;

/**
 * @mixin ApiTestCase
 */
trait JwtAuthenticatedClientTrait
{
    /**
     * Creates an instance of HttpClient with JWT for given or fixture user.
     */
    protected static function createAuthenticatedClient(?string $username = null, ?string $password = null): Client
    {
        $response = self::createClient()->request(
            method: 'POST',
            url: '/api/login_check',
            options: [
                'json' => [
                    'email' => $username ?? UserFixtures::FIXTURE_USER_EMAIL,
                    'password' => $password ?? UserFixtures::FIXTURE_USER_PASSWORD,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $responseData = $response->toArray();
        if (!isset($responseData['token'])) {
            throw new \Exception('Failed to get JWT for test');
        }

        return self::createClient(defaultOptions: [
            'headers' => [
                'Authorization' => 'Bearer '.$responseData['token'],
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }
}
