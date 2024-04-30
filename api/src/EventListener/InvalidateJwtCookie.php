<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * @psalm-api Event listener is called automatically by Symfony
 */
#[AsEventListener(LogoutEvent::class)]
final class InvalidateJwtCookie
{
    public function __invoke(LogoutEvent $event): void
    {
        $response = $event->getResponse() ?? new Response();

        $response->headers->clearCookie('Bearer');
        $response->setStatusCode(Response::HTTP_NO_CONTENT);

        $event->setResponse($response);
    }
}
