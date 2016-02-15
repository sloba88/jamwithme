<?php

namespace Jam\UserBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Happyr\Google\AnalyticsBundle\Service\Tracker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Jam\UserBundle\Entity\User;

class RegistrationConfirmedListener implements EventSubscriberInterface
{
    private $tracker;

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onRegistrationConfirmed',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        );
    }

    public function __construct(Tracker $tracker)
    {
        $this->tracker = $tracker;
    }

    public function onRegistrationConfirmed(GetResponseUserEvent $event)
    {
        /**
         * @var User
         */
        $user = $event->getUser();

        /* send data to GA */
        $data = array(
            'uid'=> $user->getId(),
            'ec'=> 'authentication',
            'ea'=> 'registered'
        );
        $this->tracker->send($data, 'event');
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        /**
         * @var User
         */
        $user = $event->getForm()->getData();

        /* send data to GA */
        $data = array(
            'uid'=> $user->getId(),
            'ec'=> 'authentication',
            'ea'=> 'register unconfirmed'
        );
        $this->tracker->send($data, 'event');
    }

}