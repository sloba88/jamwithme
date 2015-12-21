<?php

namespace Jam\ApiBundle\Controller;

use Elastica\Filter\BoolNot;
use Elastica\Query\Filtered;
use Elastica\Filter\Ids;
use Elastica\Query\MatchAll;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Jam\CoreBundle\Entity\Compatibility;

class CompatibilityController extends FOSRestController
{
    /**
     * @Get("/compatibility/{id}", name="api_compatibility")
     */
    public function getCompatibilityAction($id)
    {
        /* @var $m \Jam\UserBundle\Entity\User */
        /* @var $me \Jam\UserBundle\Entity\User */
        $m = $this->getDoctrine()->getManager()->getReference('JamUserBundle:User', $id);
        $me = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $query = "SELECT compatibility
            FROM JamCoreBundle:Compatibility compatibility
            JOIN JamUserBundle:User musician
            WHEN (compatibility.musician2 = " . $m->getId() . " AND compatibility.musician = " .$me->getId() . " )
            OR (compatibility.musician = " . $m->getId() . " AND compatibility.musician2 = " .$me->getId() . " ) ";

        $compatibility = $em->createQuery($query)->getResult();

        if (!$compatibility){
            $compatibility = new Compatibility();
            $compatibility->setMusician($me);
            $compatibility->setMusician2($m);
            $compatibility->calculate();

            $em->persist($compatibility);
            $em->flush();
        }else{
            $compatibility = $compatibility[0];
        }

        $view = $this->view($compatibility->getValue(), 200);

        return $this->handleView($view);
    }
}
