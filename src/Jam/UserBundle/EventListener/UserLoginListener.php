<?php

namespace Jam\UserBundle\EventListener;

use Happyr\Google\AnalyticsBundle\Service\Tracker;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Jam\UserBundle\Entity\User;

class UserLoginListener
{
    private $session;

    private $tracker;

    public function __construct(Session $session, Tracker $tracker)
    {
        $this->session = $session;

        $this->tracker = $tracker;
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

        /* send data to GA */
        $data = array(
            'uid'=> $user->getId(),
            'ec'=> 'authentication',
            'ea'=> 'login'
        );
        $this->tracker->send($data, 'event');
    }

}