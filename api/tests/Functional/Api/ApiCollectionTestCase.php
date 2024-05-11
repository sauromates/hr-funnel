<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\Persistence\ManagerRegistry;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Test\Factories;

/**
 * Base class for API resources' collections testing.
 *
 * Allows to avoid massive test code duplication by running a number of
 * default operations for collection tests and assertions.
 *
 * By default, provided with resource class, factory class and API endpoint
 * it will automatically create collection items for tests and run basic tests
 * for response object (status, headers, metadata, JSON schema).
 *
 * @template T of object
 */
abstract class ApiCollectionTestCase extends ApiTestCase
{
    use Factories;

    protected function setUp(): void
    {
        if (null === $factory = static::getFactory()) {
            return;
        }

        // Create items for at least 3 page collection (hence the minimum of 91)
        $factory::createMany(random_int(91, 199));
    }

    /**
     * Specifies API endpoint (i.e. `/api/users`).
     *
     * @return non-empty-string
     */
    abstract protected static function getPath(): string;

    /**
     * Specifies a factory to use.
     *
     * If a factory doesn't exist for tested resource, items won't be created
     * and developer would need to prepare data manually.
     *
     * @return class-string<ModelFactory<T>>|null
     */
    abstract protected static function getFactory(): ?string;

    /**
     * Specifies Entity or ApiResource to work with.
     *
     * @return class-string<T>
     */
    abstract protected static function getResource(): string;

    /**
     * Runs predefined set of assertions against current response object.
     *
     * NOTE that a request MUST be made in test before calling this method!
     */
    protected function assertCollectionResponseIsSuccessfull(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(static::getResource());

        $resourcePath = static::getPath();
        $page = static::getPage();
        $lastPage = (int) ceil(static::getItemsCount() / static::getPageLimit());
        $setPage = fn (int $page): string => sprintf('%s?page=%d', $resourcePath, $page);

        $expectedMetadata = [
            '@id' => $setPage($page),
            '@type' => 'hydra:PartialCollectionView',
            'hydra:first' => $setPage(1),
            'hydra:last' => $setPage($lastPage),
            'hydra:next' => $setPage($page + 1),
        ];

        if ($page > 1) {
            $expectedMetadata['hydra:previous'] = $setPage($page - 1);
        }

        $this->assertJsonContains([
            '@context' => '/contexts/'.$this->getResourceShortName(),
            '@id' => $resourcePath,
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => static::getItemsCount(),
            'hydra:view' => $expectedMetadata,
        ]);
    }

    /**
     * Always use page > 1 to validate pagination properly.
     *
     * @return positive-int
     */
    protected static function getPage(): int
    {
        return 2;
    }

    /**
     * API Platform defaults to 30 items per page.
     *
     * @return positive-int
     */
    protected static function getPageLimit(): int
    {
        return 30;
    }

    /**
     * Returns total amount of items in the database.
     *
     * Provided with factory, method will use it as helper (which is quick and recommended).
     * If there' no factory specified a Doctrine repository for current resource will be used.
     *
     * @return int<0, max>
     */
    protected function getItemsCount(): int
    {
        $factory = static::getFactory();
        if (null !== $factory) {
            return $factory::repository()->count();
        }

        /** @var ?ManagerRegistry $doctrine */
        $doctrine = self::getContainer()->get('doctrine');
        if (null === $doctrine) {
            throw new \Exception('No repositories available for items count.');
        }

        $repository = $doctrine->getManager()->getRepository(static::getResource());

        return \count($repository->findAll());
    }

    /**
     * Creates an endpoint path with pagination query.
     *
     * It's highly recommended to use this method instead of providing
     * plain string to HttpClient::request method because pagination
     * may be incomplete without the query (no `previous` link, for instance).
     *
     * @return string
     */
    protected function getCollectionEndpoint(): string
    {
        return sprintf('%s?page=%d', static::getPath(), static::getPage());
    }

    /**
     * Extracts class' short name from FQCN.
     *
     * @return non-empty-string
     */
    private function getResourceShortName(): string
    {
        $reflection = new \ReflectionClass(static::getResource());

        return $reflection->getShortName();
    }
}
