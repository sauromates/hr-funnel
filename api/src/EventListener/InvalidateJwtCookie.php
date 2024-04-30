<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(LogoutEvent::class)]
final class InvalidateJwtCookie
{
    public function __invoke(LogoutEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->clearCookie('Bearer');

        $event->setResponse($response);
    }
}
