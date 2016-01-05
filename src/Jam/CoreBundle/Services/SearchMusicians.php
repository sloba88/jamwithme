<?php

namespace Jam\CoreBundle\Services;

use Elastica\Filter\Bool;
use Elastica\Filter\BoolNot;
use Elastica\Filter\Ids;
use Elastica\Filter\Nested;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Filtered;
use Elastica\Query\MatchAll;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Jam\CoreBundle\Entity\Search;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class SearchMusicians {

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TransformedFinder
     */
    private $elasticUserFinder;

    /**
     * @param TransformedFinder $finder
     */
    public function setElasticUserFinder(TransformedFinder $finder)
    {
        $this->elasticUserFinder = $finder;
    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get search result(s) based on $search object parameters
     *
     * @param Search $search
     * @return array
     */
    public function getElasticSearchResult(Search $search, $request = array())
    {
        $me = $this->tokenStorage->getToken()->getUser();

        //no limit is used for map view
        if (isset($request['limit']) && $request['limit'] === '0') {
            $perPage = 5000;
            $page = 1;
        } else {
            $perPage = 20;
            $page = isset($request['page']) ? intval($request['page']) : 1;
        }

        $distance = intval($search->getDistance());

        if ($distance > 50) {
            $distance = 50;
        }

        $elasticaQuery = new MatchAll();

        if ($search->getGenres() != ''){
            $elasticaQuery = $this->addToNestedFilter(new Terms('musician2.genres.genre.id', explode(",", $search->getGenres())), $elasticaQuery);
        }

        if ($search->getInstruments() != ''){
            $elasticaQuery = $this->addToNestedFilter(new Terms('musician2.instruments.instrument.id', explode(",", $search->getInstruments())), $elasticaQuery);
        }

        if ($search->getIsTeacher()){
            $boolFilter = new Bool();
            $filter1 = new Term();
            $filter1->setTerm('musician2.isTeacher', '1');
            $boolFilter->addMust($filter1);

            $nested = new Nested();
            $nested->setPath("musician2");
            $nested->setFilter($boolFilter);

            $elasticaQuery = new Filtered($elasticaQuery, $nested);
        }

        if ($request['distance'] && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'musician2.pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '50') . 'km'
            );

            $nested = new Nested();
            $nested->setPath("musician2");
            $nested->setFilter($locationFilter);

            $elasticaQuery = new Filtered($elasticaQuery, $nested);
        }

        //kick me out of result set
        $idsFilter = new Ids();
        $idsFilter->setIds(array($me->getId()));
        $elasticaBool = new BoolNot($idsFilter);
        $elasticaQuery = new Filtered($elasticaQuery, $elasticaBool);

        //show my compatibilities
        $boolFilter = new Bool();
        $filter1 = new Term();
        $filter1->setTerm('musician.id', $me->getId());
        $boolFilter->addMust($filter1);
        $elasticaQuery = new Filtered($elasticaQuery, $boolFilter);

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);
        $query->addSort(array('musician2.isJammer' => array('order' => 'desc'), 'value' => array('order' => 'desc')));

        $musicians = $this->elasticUserFinder->find($query);

        return $musicians;
    }

    private function addToNestedFilter($categoryQuery, $elasticaQuery)
    {
        $nested = new Nested();
        $nested->setPath("musician2");
        $nested->setFilter($categoryQuery);

        return new Filtered($elasticaQuery, $nested);
    }
}