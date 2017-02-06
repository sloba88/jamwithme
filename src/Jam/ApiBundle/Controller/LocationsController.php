<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class LocationsController extends FOSRestController
{
    /**
     * @Route("/locations", name="api_locations")
     * @Cache(public=true, maxage="1500", smaxage="1500")
     */
    public function getAction(Request $request)
    {
        $request->getSession()->save();
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT g.administrative_area_level_3 AS id, g.administrative_area_level_3 AS text
                FROM JamLocationBundle:Location g
                WHERE g.administrative_area_level_3 != ''
                GROUP BY text
                HAVING COUNT(g.id) > 4
                ORDER BY g.country, text ASC"
            )
            ->useResultCache(true);

        $data = $query->getResult();
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
