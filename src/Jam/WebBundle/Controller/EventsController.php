<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class EventsController extends Controller
{
    /**
     * @Route("/events", name="events", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if (!$this->getUser()->getLocation()) {
            return $this->redirect($this->generateUrl('home'));
        } else {
            if (!$this->getUser()->getLocation()->getCountry() == 'Finland') {
                return $this->redirect($this->generateUrl('home'));
            }
        }
    }
}
