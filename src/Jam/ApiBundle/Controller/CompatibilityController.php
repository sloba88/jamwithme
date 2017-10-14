<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;

class CompatibilityController extends FOSRestController
{
    /**
     * @Get("/compatibility/{id}", name="api_compatibility")
     */
    public function getCompatibilityAction($id)
    {
        $me = $this->getUser();

        $musician = $this->get('search.musicians')->getOneMusician($id, $me);

        $score = round($musician[0]->getResult()->getScore(), 2);

        if ($score >= 1 ) {
            $compatibility = 'high';
        } else if ($score >= 0.4 ) {
            $compatibility = 'medium';
        } else {
            $compatibility = 'low';
        }

        $view = $this->view($compatibility, 200);

        return $this->handleView($view);
    }
}
