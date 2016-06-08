<?php

namespace Jam\ApiBundle\Controller\Admin;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Event\FormEvent;
use Jam\CoreBundle\Entity\Search;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\Form;

class MusiciansController extends FOSRestController
{
    /**
     * @Get("/musicians/map", name="admin_musicians_find")
     */
    public function findAction()
    {
        $musicians = $this->getDoctrine()
                ->getRepository('JamUserBundle:User')
                ->findAll();

        $musicians_data = array();

        foreach($musicians AS $m){

            if ($m->getLocation() && $m->getLocation()->getLat() != '') {
                $data_array = array(
                    'username' => $m->getUsername(),
                    'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                    'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                    'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                );

                array_push($musicians_data, $data_array);
            }
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $musicians_data,
        ), 200);

        return $this->handleView($view);
    }
}
