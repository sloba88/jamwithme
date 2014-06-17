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
        $message = $eventArgs->getDocument();
        $dm = $eventArgs->getDocumentManager();
        $productReflProp = $dm->getClassMetadata('JamMessageBundle:Message')
            ->reflClass->getProperty('from');
        $productReflProp->setAccessible(true);
        $productReflProp->setValue(
            $message, $this->em->getReference('JamUserBundle:User', $message->getFrom())
        );
    }
}