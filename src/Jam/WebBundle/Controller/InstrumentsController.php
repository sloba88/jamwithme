<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InstrumentsController extends Controller
{
    /**
     * @Route("/api/instruments", name="api_instruments", options={"expose"=true})
     * @Template()
     */
    public function getAction(Request $request)
    {
        $results = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Instrument')
            ->findAll();

        $res = array();

        foreach ($results AS $k=>$g){
            $res[$k]['id'] = $g->getId();
            $res[$k]['name'] = $g->getName();
        }

        return new JsonResponse($res);
    }
}
