<?php

namespace Jam\WebBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{
    /**
     * @Route("/users-map", name="admin_users_map")
     * @Template
     */
    public function mapAction(Request $request)
    {

    }

    /**
     * @Route("/users-list", name="admin_users_list")
     * @Template
     */
    public function listAction()
    {
        $musicians = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->findAll();

        return array('musicians' => $musicians);
    }
}
