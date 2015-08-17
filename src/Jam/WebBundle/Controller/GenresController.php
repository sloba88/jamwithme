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
        $results = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Genre')
            ->findAll();

        $res = array();

        foreach ($results AS $k=>$g){
            $res[$k]['id'] = $g->getId();
            $res[$k]['text'] = $g->getName();
        }

        return new JsonResponse($res);
    }
}
