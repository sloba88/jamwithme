<?php

namespace Jam\UserBundle\EventListener;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Jam\UserBundle\Entity\User;

class UserLocaleListener
{

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /**
         * @var User
         */
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }
    }

}