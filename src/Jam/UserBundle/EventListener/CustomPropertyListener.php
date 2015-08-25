<?php

namespace Jam\UserBundle\EventListener;

use FOS\ElasticaBundle\Event\TransformEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomPropertyListener implements EventSubscriberInterface
{
    private $anotherService;


    public function addCustomProperty(TransformEvent $event)
    {
        $object = $event->getObject();
        //$custom = $this->anotherService->calculateCustom($event->getObject());

     //   $object->set('compatibility', '100');
    }

    public static function getSubscribedEvents()
    {
        return array(
            TransformEvent::POST_TRANSFORM => 'addCustomProperty',
        );
    }
}