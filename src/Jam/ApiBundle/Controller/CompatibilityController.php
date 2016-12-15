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
        //TODO: get compatibility from ELASTICSEARCH
        $view = $this->view(1, 200);

        return $this->handleView($view);
    }
}
