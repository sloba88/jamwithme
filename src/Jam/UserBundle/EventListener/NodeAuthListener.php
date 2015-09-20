<?php

namespace Jam\UserBundle\EventListener;

use Jam\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NodeAuthListener
{
    private $tokenStorage;
    private $session;

    public function __construct(TokenStorage $tokenStorage, Session $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    public function onSecurityInteractiveLogin(GetResponseEvent $event)
    {

        if (is_object($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();

            if($user instanceof User) {
                $this->session->set('__userId', $this->tokenStorage->getToken()->getUser()->getId());
                $this->session->set('__username', $this->tokenStorage->getToken()->getUser()->getUsername());
            }
        }
    }

}