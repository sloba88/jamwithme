<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;

class ErrorController extends FOSRestController
{
    /**
     * @Post("/js-error-report", name="api_js_error_report")
     */
    public function getBrandsAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $data = $request->request->all();

        $logger = $this->get('logger');
        $logger->error('javascript error: ' . $data['message'] . ' on url ' . $data['url'] . ' on line number ' . $data['lineNumber'] );

        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
