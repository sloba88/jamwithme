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
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT i.id, i.name AS text FROM JamCoreBundle:Instrument i"
            );

        $res = $query->getResult();

        return new JsonResponse($res);
    }

    /**
     * @Route("/api/instruments/skills")
     * @Template()
     */
    public function getSkillsAction()
    {
        //TODO: put this to some config
        $translator = $this->get('translator');
        $res = array(
            array(
                'id' => 1,
                'text' => $translator->trans('value.beginner')
            ),
            array(
                'id' => 2,
                'text' => $translator->trans('value.average')
            ),
            array(
                'id' => 3,
                'text' => $translator->trans('value.advanced')
            ),
            array(
                'id' => 4,
                'text' => $translator->trans('value.semi-professional')
            ),
            array(
                'id' => 5,
                'text' => $translator->trans('professional')
            )
        );

        return new JsonResponse($res);
    }
}
