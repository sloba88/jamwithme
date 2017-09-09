<?php

namespace Jam\CoreBundle\Services;

use Elastica\Query\Ids;
use Elastica\Query\Terms;
use Elastica\Query;
use Elastica\Query\Match;
use Elastica\Query\BoolQuery;
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

        if ($search->getDistance() > 100) {
            $distance = 100;
        } else {
            $distance = $search->getDistance();
        }

        $q = new Query();
        $elasticaQuery = new BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());

        if ($search->getGenres() != ''){
            //search by selected filter genres
            $genres = $this->genreFinder->find(str_replace("genres", "id", $search->getGenres()));

            foreach($genres AS $d) {
                if ($d->getCategory()->getName() == $d->getName()) {
                    //if its also the name of category check category
                    $elasticaQuery->addShould(new Terms('genres.genre.category.id', array($d->getCategory()->getId())));
                }
            }

            $elasticaQuery->addMust(new Terms('genres.genre.id', explode(",", $search->getGenres())));
        }

        if ($search->getInstruments() != ''){

            $instruments = $this->instrumentFinder->find(str_replace("instruments", "id", $search->getInstruments()));
            $q = new BoolQuery();

            foreach($instruments AS $d) {
                if ($d->getCategory()->getName() == $d->getName() || ($d->getCategory()->getId() == 1 && $d->getId() == 37)) {
                    //if its also the name of category check category
                    $q->addShould(new Terms('instruments.instrument.category.id', array($d->getCategory()->getId())));
                }

                if ($d->getId() == 263) {
                    //if its also the name of category check category
                    $q->addShould(new Terms('instruments.instrument.id', array(26)));
                }

                if ($d->getId() == 289) {
                    //if its keyboard check synthesizer and vice versa
                    $q->addShould(new Terms('instruments.instrument.id', array(178)));
                }

                if ($d->getId() == 178) {
                    //if its keyboard check synthesizer and vice versa
                    $q->addShould(new Terms('instruments.instrument.id', array(289)));
                }
            }

            $q->addShould(new Terms('instruments.instrument.id', explode(",", $search->getInstruments())));
            $elasticaQuery->addMust($q);
        }

        //prefer matches with my other genres
        if (count($me->getGenresIdsArray()) > 0) {
            $elasticaQuery->addShould(new Terms('genres.genre.id', $me->getGenresIdsArray()));

            $genresCategories = $me->getGenres()->map(function($genre){
                return $genre->getGenre()->getCategory()->getId();
            })->toArray();

            //also check genre categories
            $elasticaQuery->addShould(new Terms('genres.genre.category.id', $genresCategories));
        }

        //add artists to the mix
        if ($me->getArtists()->count() > 0) {
            $ids = $me->getArtists()->map(function($artist){
                return $artist->getId();
            })->toArray();

            $elasticaQuery->addShould(new Terms('artists.id', $ids));
        }

        //add commitment to the list
        if ($me->getCommitment() > 0) {
            $elasticaQuery->addShould(new Match('commitment', array('query' => $me->getCommitment())));
        }

        if ($me->getAge() > 0) {
            $elasticaQuery->addShould(new Match('age', array('query' => $me->getAge())));
        }

        if ($distance && $me->getLat()){
            $locationFilter = new \Elastica\Query\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '100') . 'km'
            );

            $elasticaQuery->addMust($locationFilter);

            //also query on the same filter to get points
            $functionScore = new Query\FunctionScore();
            $functionScore->addFunction('gauss', array(
                'pin' => array(
                    'origin' => array(
                        'lat' => $me->getLat(),
                        'lon' => $me->getLon()
                    ),
                    'offset' => '2km',
                    'scale' => '3km'
                )
            ));
            $elasticaQuery->addShould($functionScore);
        } else {
            //get based on city
            if ($search->getAdministrativeAreaLevel3()) {
                $elasticaQuery->addMust(new Match('location.administrative_area_level_3', array('query' => $search->getAdministrativeAreaLevel3())));
            }
        }

        if ($search->getIsTeacher()){
            $elasticaQuery->addMust(new Match('isTeacher', array('query' => 1, 'boost' => 0)));

            $data = array(
                'uid'=> $me->getId(),
                'ec'=> 'filter',
                'ea'=> 'teachers'
            );
            $this->tracker->send($data, 'event');
        }

        //kick me out of result set
        $idsFilter = new Query\Ids();
        $idsFilter->setIds(array($me->getId()));
        $elasticaQuery->addMustNot($idsFilter);

        $query = new Query($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);

        return $this->elasticUsersFinder->findHybrid($query);
    }

    public function getElasticSearchPublicResult($request = array())
    {
        //no limit is used for map view
        if (isset($request['limit']) && $request['limit'] === '0') {
            $perPage = 5000;
            $page = 1;
        } else {
            $perPage = 20;
            $page = isset($request['page']) ? intval($request['page']) : 1;
        }

        $q = new Query();
        $elasticaQuery = new BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());

        if (isset($request['genres']) && $request['genres'] != ''){

            $genres = $this->genreFinder->find($request['genres']);
            foreach($genres AS $d) {
                if ($d->getCategory()->getName() == $d->getName()) {
                    //if its also the name of category check category
                    $elasticaQuery->addShould(new Terms('genres.genre.category.id', array($d->getCategory()->getId())));
                }
            }

            $elasticaQuery->addMust(new Terms('genres.genre.id', explode(",", $request['genres'])));
        }

        if (isset($request['instruments']) && $request['instruments'] != ''){
            $instruments = $this->instrumentFinder->find($request['instruments']);

            $q = new BoolQuery();

            foreach($instruments AS $d) {
                if ($d->getCategory()->getName() == $d->getName() || ($d->getCategory()->getId() == 1 && $d->getId() == 37)) {
                    //if its also the name of category check category
                    $elasticaQuery->addShould(new Terms('instruments.instrument.category.id', array($d->getCategory()->getId())));
                }

                if ($d->getId() == 263) {
                    //if its also the name of category check category
                    $q->addShould(new Terms('instruments.instrument.id', array(26)));
                }
            }

            $q->addShould(new Terms('instruments.instrument.id', explode(",", $request['instruments'])));
            $elasticaQuery->addMust($q);
        }

        if (isset($request['locations']) && $request['locations'] != '') {
            $elasticaQuery->addMust(new Match('location.administrative_area_level_3', array('query' => $request['locations'])));
        }

        if (isset($request['isTeacher']) && $request['isTeacher']){
            $elasticaQuery->addMust(new Match('isTeacher', array('query' => 1, 'boost' => 0)));
        }

        $query = new Query($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);

        $query->addSort(array('profileFulfilment' => array('order' => 'desc')));

        return $this->elasticUsersFinder->find($query);
    }

    public function getOneMusician($userId, $me)
    {
        $q = new Query();
        $elasticaQuery = new BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());

        $functionScore = new Query\FunctionScore();
        $functionScore->addFunction('gauss', array(
            'pin' => array(
                'origin' => array(
                    'lat' => $me->getLat(),
                    'lon' => $me->getLon()
                ),
                'offset' => '2km',
                'scale' => '3km'
            )
        ));
        $elasticaQuery->addShould($functionScore);

        //prefer matches with my other genres
        if (count($me->getGenresIdsArray()) > 0) {
            $elasticaQuery->addShould(new Query\Terms('genres.genre.id', $me->getGenresIdsArray()));

            $genresCategories = $me->getGenres()->map(function($genre){
                return $genre->getGenre()->getCategory()->getId();
            })->toArray();

            //also check genre categories
            $elasticaQuery->addShould(new Query\Terms('genres.genre.category.id', $genresCategories));
        }

        //add artists to the mix
        if ($me->getArtists()->count() > 0) {
            $ids = $me->getArtists()->map(function($artist){
                return $artist->getId();
            })->toArray();

            $elasticaQuery->addShould(new Query\Terms('artists.id', $ids));
        }

        //add commitment to the list
        if ($me->getCommitment() > 0) {
            $elasticaQuery->addShould(new Match('commitment', array('query' => $me->getCommitment())));
        }

        if ($me->getAge() > 0) {
            $elasticaQuery->addShould(new Match('age', array('query' => $me->getAge())));
        }

        $idsFilter = new Ids();
        $idsFilter->setIds(array($userId));
        $elasticaQuery->addMust($idsFilter);

        $query = new Query($elasticaQuery);

        return $this->elasticUsersFinder->findHybrid($query);
    }

    public function getElasticSearchPublicResultMap(array $location)
    {
        //no limit is used for map view
        $perPage = 200;
        $page = 1;
        $distance = 30;

        $q = new Query();
        $elasticaQuery = new BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());

        if ($location['lat'] && $location['lng']){
            $locationFilter = new \Elastica\Query\GeoDistance(
                'pin',
                array('lat' => floatval($location['lat']), 'lon' => floatval($location['lng'])),
                ($distance ? $distance : '100') . 'km'
            );
            $elasticaQuery->addMust($locationFilter);
        }

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);

        //sort by compatibility here
        return $this->elasticUsersFinder->find($query);
    }

    public function getElasticSimilarUsersResult($user)
    {
        $query = new Query();;

        $query->setSize(5);
        $query->setFrom(0);

        $mltQuery = new Query\MoreLikeThis();
        $mltQuery->setFields(array('instruments.instrument.id', 'artists.id', 'genres.genre.id', 'genres.genre.category.id', 'age', 'location.administrative_area_level_3'));
        $mltQuery->setMinTermFrequency(1);
        $mltQuery->setMaxQueryTerms(12);

        $mltQuery->setLike(array(array('_id' => $user->getId())));

        $query->setQuery($mltQuery);

        return $this->elasticUsersFinder->find($query);
    }

    public function getMusiciansByJam($jam)
    {
        /* @var $jam \Jam\CoreBundle\Entity\Jam */

        $q = new Query();
        $elasticaQuery = new BoolQuery($q);

        if ($jam->getLocation()){
            if ($jam->getLocation()->getLat() && $jam->getLocation()->getLng()) {
                $locationFilter = new \Elastica\Query\GeoDistance(
                    'pin',
                    array('lat' => floatval($jam->getLocation()->getLat()), 'lon' => floatval($jam->getLocation()->getLng())),
                    100 . 'km'
                );
                $elasticaQuery->addMust($locationFilter);
            } else {
                if ($jam->getLocation()->getAdministrativeAreaLevel3()) {
                    $elasticaQuery->addMust(new Match('location.administrative_area_level_3', array('query' => $jam->getLocation()->getAdministrativeAreaLevel3())));
                }
            }
        }

        $elasticaQuery->addMust(new Terms('instruments.instrument.id', $jam->getInstrumentsIds()));

        if (count($jam->getGenresIds()) > 0) {
            $elasticaQuery->addMust(new Terms('genres.genre.id', $jam->getGenresIds()));
        }

        $query = new Query($elasticaQuery);

        $query->setSize(100);
        $query->setFrom(0);

        return $this->elasticUsersFinder->find($query);
    }
}