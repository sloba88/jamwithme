<?php

namespace Jam\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use Happyr\Google\AnalyticsBundle\Service\Tracker;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileEditedListener implements EventSubscriberInterface
{
    private $userManager;

    private $session;

    private $tracker;

    public function __construct(UserManagerInterface $userManager, Session $session, Tracker $tracker)
    {
        $this->userManager = $userManager;
        $this->session = $session;
        $this->tracker = $tracker;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onEditSuccess',
        );
    }

    public function onEditSuccess(Event $event)
    {
        if ($event instanceof FormEvent) {

            /** @var $user \Jam\UserBundle\Entity\User */
            $user = $event->getForm()->getData();

            if ($user->getLocation()->getAddress() == "" || $user->getLocation()->getAddress() == NULL) {
                //unset location
                $user->setLocation(null);

                $this->userManager->updateUser($user);
            }

            $this->session->set('_locale', $user->getLocale());

            /* send data to GA */
            $data = array(
                'uid'=> $user->getId(),
                'ec'=> 'profile',
                'ea'=> 'edited'
            );
            $this->tracker->send($data, 'event');
        }
    }
}
