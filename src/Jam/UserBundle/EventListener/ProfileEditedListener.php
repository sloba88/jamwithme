<?php

namespace Jam\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileEditedListener implements EventSubscriberInterface
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager, Session $session)
    {
        $this->userManager = $userManager;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onEditSuccess',
        );
    }

    public function onEditSuccess(FormEvent $event)
    {
        /** @var $user \Jam\UserBundle\Entity\User */
        $user = $event->getForm()->getData();

        if ($user->getLocation()->getAddress() == "" || $user->getLocation()->getAddress() == NULL){
            //unset location
            $user->setLocation(null);

            $this->userManager->updateUser($user);
        }

        $this->session->set('_locale', $user->getLocale());
    }
}
