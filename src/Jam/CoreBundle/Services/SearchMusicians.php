<?php

namespace Jam\CoreBundle\Services;

use Elastica\Filter\BoolFilter;
use Elastica\Filter\BoolNot;
use Elastica\Filter\BoolOr;
use Elastica\Filter\Ids;
use Elastica\Filter\Nested;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Filtered;
use Elastica\Query\MatchAll;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Happyr\Google\AnalyticsBundle\Service\Tracker;
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
    private $elasticCompatibilityFinder;

    /**
     * @var TransformedFinder
     */
    private $elasticUsersFinder;

    /**
     * @var TransformedFinder
     */
    private $genreFinder;

    /**
     * @var TransformedFinder
     */
    private $instrumentFinder;

    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * @param TransformedFinder $finder
     */
    public function setElasticCompatibilityFinder(TransformedFinder $finder)
    {
        $this->elasticCompatibilityFinder = $finder;
    }

    /**
     * @param TransformedFinder $finder
     */
    public function setElasticUsersFinder(TransformedFinder $finder)
    {
        $this->elasticUsersFinder = $finder;
    }

    /**
     * @param TransformedFinder $finder
     */
    public function setGenreFinder(TransformedFinder $finder)
    {
        $this->genreFinder = $finder;
    }

    /**
     * @param TransformedFinder $finder
     */
    public function setInstrumentFinder(TransformedFinder $finder)
    {
        $this->instrumentFinder = $finder;
    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setTracker(Tracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Get search result(s) based on $search object parameters
     *
     * @param Search $search
     * @return array
     */
    public function getElasticSearchResult(Search $search, $request = array())
    {
        $me = $search->getCreator();

        //no limit is used for map view
        if (isset($request['limit']) && $request['limit'] === '0') {
            $perPage = 5000;
            $page = 1;
        } else {
            $perPage = 20;
            $page = isset($request['page']) ? intval($request['page']) : 1;
        }

        $distance = intval($search->getDistance());

        if ($distance > 100) {
            $distance = 100;
        }

        /* send data to GA */
        $data = array(
            'uid'=> $me->getId(),
            'ec'=> 'search',
            'ea'=> 'distance',
            'el'=> $distance . 'km',
            'ev'=> $distance
        );
        $this->tracker->send($data, 'event');

        $elasticaQuery = new MatchAll();

        if ($search->getGenres() != ''){

            $genres = $this->genreFinder->find(str_replace("genres", "id", $search->getGenres()));
            $boolFilter = new BoolOr();

            foreach($genres AS $d) {
                if ($d->getCategory()->getName() == $d->getName()) {
                    //if its also the name of category check category
                    $boolFilter->addFilter(new Terms('musician2.genres.genre.category.id', array($d->getCategory()->getId())));
                }
            }

            $boolFilter->addFilter(new Terms('musician2.genres.genre.id', explode(",", $search->getGenres())));
            $elasticaQuery = $this->addToNestedFilter($boolFilter, $elasticaQuery);
        }

        if ($search->getInstruments() != ''){
            $instruments = $this->instrumentFinder->find(str_replace("instruments", "id", $search->getInstruments()));
            $boolFilter = new BoolOr();

            foreach($instruments AS $d) {
                if ($d->getCategory()->getName() == $d->getName() || ($d->getCategory()->getId() == 1 && $d->getId() == 37)) {
                    //if its also the name of category check category
                    $boolFilter->addFilter(new Terms('musician2.instruments.instrument.category.id', array($d->getCategory()->getId())));
                }

                if ($d->getId() == 263) {
                    //if its also the name of category check category
                    $boolFilter->addFilter(new Terms('musician2.instruments.instrument.id', array(26)));
                }
            }

            $boolFilter->addFilter(new Terms('musician2.instruments.instrument.id', explode(",", $search->getInstruments())));
            $elasticaQuery = $this->addToNestedFilter($boolFilter, $elasticaQuery);
        }

        if ($search->getIsTeacher()){
            $boolFilter = new BoolFilter();
            $filter1 = new Term();
            $filter1->setTerm('musician2.isTeacher', '1');
            $boolFilter->addMust($filter1);

            $nested = new Nested();
            $nested->setPath("musician2");
            $nested->setFilter($boolFilter);

            $elasticaQuery = new Filtered($elasticaQuery, $nested);

            /* send data to GA */
            $data = array(
                'uid'=> $me->getId(),
                'ec'=> 'filter',
                'ea'=> 'teachers'
            );
            $this->tracker->send($data, 'event');
        }

        if ($distance && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'musician2.pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '100') . 'km'
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
        $boolFilter = new BoolFilter();
        $filter1 = new Term();
        $filter1->setTerm('musician.id', $me->getId());
        $boolFilter->addMust($filter1);
        $elasticaQuery = new Filtered($elasticaQuery, $boolFilter);

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);

        //sort by compatibility here
        $query->addSort(array('musician2.isJammer' => array('order' => 'desc'), 'value' => array('order' => 'desc')));

        $musicians = $this->elasticCompatibilityFinder->find($query);

        return $musicians;
    }

    public function getElasticSearchPublicResult(array $location)
    {
        //no limit is used for map view
        $perPage = 200;
        $page = 1;
        $distance = 30;

        $elasticaQuery = new MatchAll();

        if ($location['lat'] && $location['lng']){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'user.pin',
                array('lat' => floatval($location['lat']), 'lon' => floatval($location['lng'])),
                ($distance ? $distance : '100') . 'km'
            );
            $elasticaQuery = new Filtered($elasticaQuery, $locationFilter);
        }

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);

        //sort by compatibility here
        $musicians = $this->elasticUsersFinder->find($query);

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