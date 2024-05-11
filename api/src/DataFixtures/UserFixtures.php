<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Tests\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public const FIXTURE_USER_EMAIL = 'user@example.com';
    public const FIXTURE_USER_PASSWORD = 'password';

    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => self::FIXTURE_USER_EMAIL,
            'password' => self::FIXTURE_USER_PASSWORD,
        ]);
    }
}
