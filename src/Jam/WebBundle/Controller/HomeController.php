<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", options={"expose"=true})
     * @Route("/teachers", name="teachers", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser() == null) {
            return $this->forward('JamUserBundle:Registration:register');
        }
    }
}
