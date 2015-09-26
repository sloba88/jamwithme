<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GenresController extends Controller
{
    /**
     * @Route("/api/genres", name="api_genres", options={"expose"=true})
     * @Template()
     */
    public function getAction(Request $request)
    {
        $request->getSession()->save();
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT g.id, g.name AS text FROM JamCoreBundle:Genre g"
            );

        $res = $query->getResult();

        return new JsonResponse($res);
    }
}
