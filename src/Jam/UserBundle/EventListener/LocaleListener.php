<?php

namespace Jam\UserBundle\EventListener;

use Jam\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LocaleListener implements EventSubscriberInterface
{
    private $tokenStorage;

    private $defaultLocale;

    public function __construct($defaultLocale = 'en', TokenStorage $tokenStorage)
    {
        $this->defaultLocale = $defaultLocale;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = new Response();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } else if ($locale = $request->query->get('lang')) {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        $response = $event->getResponse();
        $response->headers->setCookie(new Cookie('language', $request->getLocale()));
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
            KernelEvents::RESPONSE => array(array('onKernelResponse', 15))
        );
    }
}