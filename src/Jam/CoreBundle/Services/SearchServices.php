<?php

namespace Jam\CoreBundle\Services;

use Elastica\Query;
use Elastica\Query\MatchAll;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SearchServices {

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TransformedFinder
     */
    private $elasticServiceFinder;

    /**
     * @param TransformedFinder $finder
     */
    public function setElasticServiceFinder(TransformedFinder $finder)
    {
        $this->elasticServiceFinder = $finder;
    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get search result(s) based on $search object parameters
     *
     * @param $distance
     * @return array
     */
    public function getElasticSearchResult($distance)
    {
        $me = $this->tokenStorage->getToken()->getUser();

        if ($distance > 100) {
            $distance = 100;
        }

        $q = new Query();
        $elasticaQuery = new Query\BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());

        if ($distance && $me->getLat()){
            $locationFilter = new \Elastica\Query\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '100') . 'km'
            );

            $elasticaQuery->addMust($locationFilter);
        }

        $query = new Query($elasticaQuery);
        $query->setSize(5000);
        $query->setFrom(0);

        return $this->elasticServiceFinder->find($query);
    }
}