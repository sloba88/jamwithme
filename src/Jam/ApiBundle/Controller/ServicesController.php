<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;

class ServicesController extends FOSRestController
{
    /**
     * @Get("/services/find", name="services_find")
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $this->getUser();

        if (!$me->getLocation()) {
            $view = $this->view(array(
                'message'    => 'No location set.',
                'success'    => false,
                'data' => array()
            ), 200);

            return $this->handleView($view);
        }

        $request->getSession()->save();
        $services = $this->get('search.services')->getElasticSearchResult($request->query->get('distance'));

        $servicesData = array();

        foreach($services AS $k=> $s){

            /* @var $s \Jam\CoreBundle\Entity\Service */

            $data_array = array(
                'displayName' => $s->getDisplayName(),
                'phone' => $s->getPhone(),
                'email' => $s->getEmail(),
                'website' => $s->getWebsite(),
                'address' => $s->getLocation()->getAddress(),
                'lat' => $s->getLat() ? $s->getLat() : '',
                'lng' => $s->getLon() ? $s->getLon() : ''
            );
            array_push($servicesData, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $servicesData,
        ), 200);

        return $this->handleView($view);
    }
}