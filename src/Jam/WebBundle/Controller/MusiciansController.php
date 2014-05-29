<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class MusiciansController extends Controller
{
    /**
     * @Route("/musicians", name="musicians")
     * @Template()
     */
    public function indexAction()
    {
        $musicians = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->findAll();

        return array('musicians' => $musicians);
    }

    /**
     * @Route("/musicians/find", name="musicians_find")
     * @Template()
     */
    public function findAction()
    {
        $musicians = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->findAll();

        $response = new JsonResponse();

        $musicians_data = array();

        foreach($musicians AS $m){
            array_push($musicians_data, array(
               'username' => $m->getUsername(),
               'lat' => $m->getLocation()->getLat(),
               'lng' => $m->getLocation()->getLng()
            ));
        }

        $response->setData(array(
            'status'    => 'success',
            'data' => $musicians_data
        ));

        return $response;
    }
}
