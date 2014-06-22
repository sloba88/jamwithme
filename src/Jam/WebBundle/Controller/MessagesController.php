<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jam\CoreBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

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
