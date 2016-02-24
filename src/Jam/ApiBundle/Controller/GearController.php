<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;

class GearController extends FOSRestController
{
    /**
     * @Get("/gear", name="api_gear")
     */
    public function getGearAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $q = $request->query->get('q');

        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT b.id, b.name FROM JamCoreBundle:MusicianGear b
                WHERE b.name LIKE :q
                GROUP BY b.name"
            )->setParameter('q', '%'.$q.'%');

        $data = $query->getResult();
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
