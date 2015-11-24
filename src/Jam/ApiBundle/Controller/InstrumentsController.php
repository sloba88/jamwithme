<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class InstrumentsController extends FOSRestController
{
    /**
     * @Get("/instruments", name="api_instruments")
     */
    public function getAction(Request $request)
    {
        $request->getSession()->save();
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT i.id, i.name AS text FROM JamCoreBundle:Instrument i"
            );

        $data = $query->getResult();
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/instruments/skills", name="api_instruments_skills")
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

        $view = $this->view($res, 200);

        return $this->handleView($view);
    }
}
