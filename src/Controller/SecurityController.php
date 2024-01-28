<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\StatusEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils,
        RateLimiterFactory  $loginRateLimiter,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface
    ): Response {

        $lastUsername = $authenticationUtils->getLastUsername();
        $user = $userRepository->findOneBy([
            'email' => $lastUsername
        ]);

        $error = $authenticationUtils->getLastAuthenticationError();
        $response = [
            'last_username' => $lastUsername,
            'error' => $error
        ];

        if ($user?->getStatus() === StatusEnum::BLOCKED) {
            $response['blocked'] = 'Blocked needs to talk with the admin!';
            return $this->render('security/login.html.twig', $response);
        }

        # TODO MOVE TO THE EVENT LISTENER
        $limiter = $loginRateLimiter->create($lastUsername);
        if ($user && $limiter->consume(1)->isAccepted() === false) {
            $user->setStatus(StatusEnum::BLOCKED);
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
        };

        return $this->render('security/login.html.twig', $response);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // if you're using service autowiring, the variable name must be:
    // "rate limiter name" (in camelCase) + "Limiter" suffix
    #[Route(path: '/rate', name: 'app_rate')]
    public function indexw(Request $request, RateLimiterFactory  $loginRateLimiter): Response
    {
        // create a limiter based on a unique identifier of the client
        // (e.g. the client's IP address, a username/email, an API key, etc.)
        $limiter = $loginRateLimiter->create($request->getClientIp());

        // the argument of consume() is the number of tokens to consume
        // and returns an object of type Limit
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        // you can also use the ensureAccepted() method - which throws a
        // RateLimitExceededException if the limit has been reached
        // $limiter->consume(1)->ensureAccepted();

        // to reset the counter
        // $limiter->reset();

        // ...

        return new Response(dd($limiter));
    }
}
