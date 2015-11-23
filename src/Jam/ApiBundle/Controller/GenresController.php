<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GenresController extends FOSRestController
{
    /**
     * @Route("/genres", name="api_genres")
     */
    public function getAction(Request $request)
    {
        $request->getSession()->save();
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT g.id, g.name AS text FROM JamCoreBundle:Genre g"
            );

        $data = $query->getResult();
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
