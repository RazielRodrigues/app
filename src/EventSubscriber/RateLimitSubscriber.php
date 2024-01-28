<?php

namespace App\EventSubscriber;

use App\Enum\StatusEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RateLimitSubscriber implements EventSubscriberInterface
{


    public function __construct(
        private RateLimiterFactory $loginRateLimiter,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManagerInterface,
        private AuthenticationUtils $authenticationUtils
    ) {
    }

    public function onKernelRequest(
        RequestEvent $event
    ): void {

        $lastUsername = $this->authenticationUtils->getLastUsername();
        if (empty($lastUsername)) {
            return;
        }

        $limiter = $this->loginRateLimiter->create($event->getRequest()->getClientIp());

        $user = $this->userRepository->findOneBy([
            'email' => $lastUsername
        ]);

        $limiter = $this->loginRateLimiter->create($lastUsername);

        if ($user && $limiter->consume(1)->isAccepted() === false) {
            $user->setStatus(StatusEnum::BLOCKED);
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
        };
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
