<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MessagesController extends Controller
{
    /**
     * @Route("/messages/", name="inbox")
     * @Template()
     */
    public function messagesAction()
    {
        return array();
    }
}
