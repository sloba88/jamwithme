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

    /**
     * @Get("/compatibility-calculate", name="api_compatibility_calculate")
     */
    public function setAllCompatibilitiesAction()
    {
        $me = $this->getUser();
        $finder = $this->container->get('fos_elastica.finder.searches.user');
        $elasticaQuery = new MatchAll();

        //get everyone in 50km radius
        if ($me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                50 . 'km'
            );
            $elasticaQuery = new Filtered($elasticaQuery, $locationFilter);
        }

        $idsFilter = new Ids();
        $idsFilter->setIds(array($me->getId()));
        $elasticaBool = new BoolNot($idsFilter);
        $elasticaQuery = new Filtered($elasticaQuery, $elasticaBool);

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize(1000000);

        $musicians = $finder->find($query);

        $em = $this->getDoctrine()->getManager();

        $em->createQuery("DELETE FROM JamCoreBundle:Compatibility compatibility WHERE compatibility.musician2 = " . $me->getId() . " OR compatibility.musician = " .$me->getId())
            ->execute();

        foreach ($musicians AS $k=> $m) {
            $compatibility = new Compatibility();
            $compatibility->setMusician($me);
            $compatibility->setMusician2($m);
            $compatibility->calculate();

            $em->persist($compatibility);
        }

        $em->flush();

        $view = $this->view(true, 200);

        return $this->handleView($view);

    }
}
