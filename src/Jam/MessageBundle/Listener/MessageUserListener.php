<?php

namespace Jam\MessageBundle\Listener;

use Doctrine\ORM\EntityManager;

class MessageUserListener
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function postLoad(\Doctrine\ODM\MongoDB\Event\LifecycleEventArgs $eventArgs)
    {
        //how to register listener only for Inbox or Message?

        $message = $eventArgs->getDocument();
        $dm = $eventArgs->getDocumentManager();

        if (get_class($message) == "Jam\MessageBundle\Document\Inbox"){
            return;
        }

        if (get_class($message) == "Jam\MessageBundle\Document\Conversation"){

            $convToUser = $dm->getClassMetadata('JamMessageBundle:Conversation')
                ->reflClass->getProperty('user');
            $convToUser->setAccessible(true);
            $convToUser->setValue(
                $message, $this->em->getReference('JamUserBundle:User', $message->getUser())
            );

            return;
        }

        $messageToUser = $dm->getClassMetadata('JamMessageBundle:Message')
            ->reflClass->getProperty('from');
        $messageToUser->setAccessible(true);
        $messageToUser->setValue(
            $message, $this->em->getReference('JamUserBundle:User', $message->getFrom())
        );

        $messageToUser = $dm->getClassMetadata('JamMessageBundle:Message')
            ->reflClass->getProperty('to');
        $messageToUser->setAccessible(true);
        $messageToUser->setValue(
            $message, $this->em->getReference('JamUserBundle:User', $message->getTo())
        );
    }
}