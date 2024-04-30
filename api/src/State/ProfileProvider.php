<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<User>
 */
final readonly class ProfileProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            throw new AccessDeniedException();
        }

        // Reload from database to reflect changes not kept in security token
        return $this->userRepository->find($currentUser->getId());
    }
}
