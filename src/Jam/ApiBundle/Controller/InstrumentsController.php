<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

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
                "SELECT i.id, i.name AS text FROM JamCoreBundle:Instrument i ORDER BY i.name"
            );

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $request->getLocale() // take locale from session or request etc.
        );

        $data = $query->getResult();

        //TODO: fix this in query!
        $results = array();
        foreach ($data as $key => $row)
        {
            $results[$key] = array(
                'id' => $row['id'],
                'text' => $row['text']
            );
        }
        array_multisort($results, SORT_ASC, $data);

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
                'text' => $translator->trans('value.professional')
            )
        );

        if ($this->getUser()->getIsTeacher()) {
            array_push($res, array(
                'id' => 10,
                'text' => $translator->trans('value.teacher')
            ));
        }

        $view = $this->view($res, 200);

        return $this->handleView($view);
    }
}
