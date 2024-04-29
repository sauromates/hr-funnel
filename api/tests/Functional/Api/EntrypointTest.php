<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

final class EntrypointTest extends ApiTestCase
{
    public function testEntrypointIsAccessible(): void
    {
        static::createClient()->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Entrypoint',
            '@id' => '/',
            '@type' => 'Entrypoint',
        ]);
    }
}
