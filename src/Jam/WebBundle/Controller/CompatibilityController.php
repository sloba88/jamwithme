<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Compatibility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CompatibilityController extends Controller
{
    /**
     * @Route("/api/compatibility/{id}", name="api_compatibility", options={"expose"=true})
     * @Template()
     */
    public function getAction($id)
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

        return new JsonResponse($compatibility->getValue());
    }
}
