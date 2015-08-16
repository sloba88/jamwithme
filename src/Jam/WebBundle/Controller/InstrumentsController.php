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
    public function getAction()
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

    /**
     * @Route("/api/instruments/skills")
     * @Template()
     */
    public function getSkillsAction()
    {
        //TODO: put this to some config
        $res = array(
            array(
                'id' => 1,
                'text' => 'Beginner'
            ),
            array(
                'id' => 2,
                'text' => 'Average'
            ),
            array(
                'id' => 3,
                'text' => 'Advanced'
            ),
            array(
                'id' => 4,
                'text' => 'Semi-Professional'
            ),
            array(
                'id' => 5,
                'text' => 'Professional'
            )
        );

        return new JsonResponse($res);
    }
}
