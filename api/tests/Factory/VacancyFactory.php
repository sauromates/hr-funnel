<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Vacancy;
use App\Enum\VacancyStatus;
use App\Repository\VacancyRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Vacancy>
 *
 * @method        Vacancy|Proxy                     create(array|callable $attributes = [])
 * @method static Vacancy|Proxy                     createOne(array $attributes = [])
 * @method static Vacancy|Proxy                     find(object|array|mixed $criteria)
 * @method static Vacancy|Proxy                     findOrCreate(array $attributes)
 * @method static Vacancy|Proxy                     first(string $sortedField = 'id')
 * @method static Vacancy|Proxy                     last(string $sortedField = 'id')
 * @method static Vacancy|Proxy                     random(array $attributes = [])
 * @method static Vacancy|Proxy                     randomOrCreate(array $attributes = [])
 * @method static VacancyRepository|RepositoryProxy repository()
 * @method static Vacancy[]|Proxy[]                 all()
 * @method static Vacancy[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Vacancy[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Vacancy[]|Proxy[]                 findBy(array $attributes)
 * @method static Vacancy[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Vacancy[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Vacancy> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Vacancy> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Vacancy> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Vacancy> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Vacancy> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Vacancy> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Vacancy> random(array $attributes = [])
 * @phpstan-method static Proxy<Vacancy> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Vacancy> repository()
 * @phpstan-method static list<Proxy<Vacancy>> all()
 * @phpstan-method static list<Proxy<Vacancy>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Vacancy>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Vacancy>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Vacancy>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Vacancy>> randomSet(int $number, array $attributes = [])
 */
final class VacancyFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->jobTitle(),
            'slug' => self::faker()->slug(),
            'shortDescription' => self::faker()->sentence(),
            'description' => self::faker()->realText(1000),
            'requirements' => self::faker()->words(random_int(3, 12)),
            'minBudget' => self::faker()->numberBetween(1, 100),
            'maxBudget' => self::faker()->numberBetween(101, 200),
            'status' => VacancyStatus::Draft,
            'createdBy' => UserFactory::new(),
        ];
    }

    protected static function getClass(): string
    {
        return Vacancy::class;
    }
}
