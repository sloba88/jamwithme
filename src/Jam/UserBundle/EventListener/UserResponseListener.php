<?php

namespace Jam\UserBundle\EventListener;

use Jam\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserResponseListener {

    protected $tokenStorage;
    protected $router;

    public function __construct(TokenStorage $tokenStorage, Router $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function onUserResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (is_object($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();

            if($user instanceof User) {

                if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    // email is not valid redirect to email reset page

                    $context = $this->router->getContext();
                    $routePath = $context->getPathInfo();
                    $routeInfo = $this->router->match($routePath);

                    if ($routeInfo['_route'] !== 'reset_email') {
                        // prevent redirect loop
                        $response = new RedirectResponse($this->router->generate('reset_email'));
                        $event->setResponse($response);
                    }
                }
            }
        }

        return $response;
    }

}