<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class GenresController extends FOSRestController
{
    /**
     * @Route("/genres", name="api_genres")
     * @Cache(public=true, maxage="1500", smaxage="1500")
     */
    public function getAction(Request $request)
    {
        $request->getSession()->save();
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT g.id, g.name AS text FROM JamCoreBundle:Genre g ORDER BY g.name"
            )
            ->useResultCache(true);

        $data = $query->getResult();
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
