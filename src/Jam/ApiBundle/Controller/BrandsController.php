<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;

class BrandsController extends FOSRestController
{
    /**
     * @Get("/brands", name="api_brands")
     */
    public function getBrandsAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $q = $request->query->get('q');

        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT b.id, b.name FROM JamCoreBundle:Brand b
                WHERE b.name LIKE :q "
            )->setParameter('q', '%'.$q.'%');

        $data = $query->getResult();
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
