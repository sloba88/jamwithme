<?php

namespace Jam\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Elastica;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Jam\CoreBundle\Entity\Compatibility;
use Jam\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Elastica\Filter\BoolNot;
use Elastica\Query\Filtered;
use Elastica\Filter\Ids;
use Elastica\Query\MatchAll;

class CompatibilityCalculator {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TransformedFinder
     */
    private $elasticaUsersFinder;

    /**
     * @var Elastica\Type
     */
    private $elasticaCompatibilityIndex;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setElasticaUsersFinder(TransformedFinder $elasticaUsersFinder)
    {
        $this->elasticaUsersFinder = $elasticaUsersFinder;
    }

    public function setElasticaCompatibilityIndex(Elastica\Type $elasticaCompatibilityIndex)
    {
        $this->elasticaCompatibilityIndex = $elasticaCompatibilityIndex;
    }

    public function calculate()
    {
        $me = $this->tokenStorage->getToken()->getUser();
        $finder = $this->elasticaUsersFinder;
        $elasticaQuery = new MatchAll();

        if (!$me instanceof User) {
            return false;
        }

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

        $this->clearPreviousCompatibilities();

        //create new compatibilities
        foreach ($musicians AS $k=> $m) {
            $compatibility = new Compatibility();
            $compatibility->setMusician($me);
            $compatibility->setMusician2($m);

            $compatibility->calculate();

            $this->em->persist($compatibility);

            $compatibility2 = new Compatibility();
            $compatibility2->setMusician($m);
            $compatibility2->setMusician2($me);
            $compatibility2->setValue($compatibility->getValue());

            $this->em->persist($compatibility2);
        }

        $this->em->flush();
    }

    private function clearPreviousCompatibilities()
    {
        $result = $this->em->createQueryBuilder()
            ->select('c')
            ->from('JamCoreBundle:Compatibility', 'c')
            ->where('c.musician = :test')
            ->orWhere('c.musician2 = :test')
            ->setParameter(':test', $this->tokenStorage->getToken()->getUser())
            ->getQuery()
            ->getResult();


        foreach ($result AS $res) {
            $this->em->remove($res);
        }

        $this->em->flush();
    }
}