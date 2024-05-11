<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Vacancy;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Assigns currently logged in user as author of the vacancy.
 *
 * In case author is already set the listener will exit early to prevent
 * it from being accidentally overwritten.
 */
#[AsEntityListener(event: Events::prePersist, entity: Vacancy::class)]
final class SetVacancyAuthor
{
    public function __construct(
        private Security $security,
    ) {}

    public function __invoke(Vacancy $vacancy, PrePersistEventArgs $event): void
    {
        if (null !== $vacancy->getCreatedBy()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException('Cannot determine the creator of a vacancy');
        }

        $vacancy->setCreatedBy($user);
    }
}
