<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // if you're using service autowiring, the variable name must be:
    // "rate limiter name" (in camelCase) + "Limiter" suffix
    #[Route(path: '/rate', name: 'app_rate')]
    public function index(Request $request, RateLimiterFactory $anonymousApiLimiter): Response
    {
        // create a limiter based on a unique identifier of the client
        // (e.g. the client's IP address, a username/email, an API key, etc.)
        $limiter = $anonymousApiLimiter->create($request->getClientIp());

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
