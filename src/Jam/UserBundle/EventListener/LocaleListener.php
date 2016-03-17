<?php

namespace Jam\UserBundle\EventListener;



use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements EventSubscriberInterface
{

    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            if ($request->query->get('lang') == 'en') {
                $request->getSession()->set('_locale', 'en');
                $request->setLocale($request->getSession()->get('_locale', $request->query->get('lang')));
            } else if ($request->query->get('lang') == 'fi') {
                $request->getSession()->set('_locale', 'fi');
                $request->setLocale($request->getSession()->get('_locale', $request->query->get('lang')));
            } else {
                $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15))
        );
    }
}