<?php

declare(strict_types=1);

namespace Security\Infrastucture\Event;

use Security\Infrastucture\RateLimiting\RequesterIdentifierProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;

class RateLimiterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactory $authenticatedRequestLimiter,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
    }
}
