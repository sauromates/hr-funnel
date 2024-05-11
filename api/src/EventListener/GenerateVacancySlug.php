<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Vacancy;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Generates a slug from vacancy title right before it's created.
 *
 * In case a slug is already set the listener will exit early to prevent
 * it from being accidentally overwritten.
 *
 * @see https://symfony.com/doc/current/components/string.html#slugger
 */
#[AsEntityListener(event: Events::prePersist, entity: Vacancy::class)]
final readonly class GenerateVacancySlug
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {}

    public function __invoke(Vacancy $vacancy, PrePersistEventArgs $event): void
    {
        if (null !== $vacancy->getSlug()) {
            return;
        }

        if (null === $title = $vacancy->getTitle()) {
            throw new \LogicException('Cannot create slug without a title');
        }

        $sanitizedTitle = mb_strtolower(trim($title));
        $slug = $this->slugger->slug($sanitizedTitle, '-', 'en')->toString();

        $vacancy->setSlug($slug);
    }
}
