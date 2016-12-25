<?php

namespace Jam\CoreBundle\Services;

use Elastica\Filter\BoolFilter;
use Elastica\Filter\BoolOr;
use Elastica\Filter\Ids;
use Elastica\Filter\Terms;
use Elastica\Query;
use Elastica\Query\Filtered;
use Elastica\Query\Match;
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

        $q = new Query();
        $elasticaQuery = new Query\BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());


        if ($search->getGenres() != ''){
            //search by selected filter genres
            $genres = $this->genreFinder->find(str_replace("genres", "id", $search->getGenres()));
            $boolFilter = new BoolOr();

            foreach($genres AS $d) {
                if ($d->getCategory()->getName() == $d->getName()) {
                    //if its also the name of category check category
                    $boolFilter->addFilter(new Terms('genres.genre.category.id', array($d->getCategory()->getId())));
                }
            }

            $boolFilter->addFilter(new Terms('genres.genre.id', explode(",", $search->getGenres())));
            $elasticaQuery->addMust(new Filtered(null, $boolFilter));
        }

        if ($search->getInstruments() != ''){

            $instruments = $this->instrumentFinder->find(str_replace("instruments", "id", $search->getInstruments()));
            $boolFilter = new BoolOr();

            foreach($instruments AS $d) {
                if ($d->getCategory()->getName() == $d->getName() || ($d->getCategory()->getId() == 1 && $d->getId() == 37)) {
                    //if its also the name of category check category
                    $boolFilter->addFilter(new Terms('instruments.instrument.category.id', array($d->getCategory()->getId())));
                }

                if ($d->getId() == 263) {
                    //if its also the name of category check category
                    $boolFilter->addFilter(new Terms('instruments.instrument.id', array(26)));
                }
            }

            $boolFilter->addFilter(new Terms('instruments.instrument.id', explode(",", $search->getInstruments())));
            $elasticaQuery->addMust(new Filtered(null, $boolFilter));
        }

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

        if ($distance && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '100') . 'km'
            );

            $elasticaQuery->addMust(new Filtered(null, $locationFilter));

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
        $idsFilter = new Ids();
        $idsFilter->setIds(array($me->getId()));
        $elasticaQuery->addMustNot(new Filtered(null, $idsFilter));

        $query = new Query($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);

        return $this->elasticUsersFinder->findHybrid($query);
    }

    public function getOneMusician($userId, $me)
    {
        $q = new Query();
        $elasticaQuery = new Query\BoolQuery($q);
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
        $elasticaQuery->addMust(new Filtered(null, $idsFilter));

        $query = new Query($elasticaQuery);

        return $this->elasticUsersFinder->findHybrid($query);
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
        return $this->elasticUsersFinder->find($query);
    }

    public function getElasticSimilarUsersResult($user)
    {
        $query = new Query();;

        $query->setSize(5);
        $query->setFrom(0);

        $mltQuery = new Query\MoreLikeThis();
        $mltQuery->setFields(array('instruments.instrument.id', 'artists.id', 'genres.genre.id', 'genres.genre.category.id', 'age', 'pin'));

        $like = array('ids' => array($user->getId()), 'min_term_freq' => 1, 'max_query_terms' => 12);

        $mltQuery->setLike($like);

        $query->setQuery($mltQuery);

        return $this->elasticUsersFinder->find($query);
    }

    public function getMusiciansByJam($jam)
    {
        /* @var $jam \Jam\CoreBundle\Entity\Jam */

        $q = new Query();
        $elasticaQuery = new Query\BoolQuery($q);

        if ($jam->getLocation()){
            if ($jam->getLocation()->getLat() && $jam->getLocation()->getLng()) {
                $locationFilter = new \Elastica\Filter\GeoDistance(
                    'user.pin',
                    array('lat' => floatval($jam->getLocation()->getLat()), 'lon' => floatval($jam->getLocation()->getLng())),
                    100 . 'km'
                );
                $elasticaQuery->addMust(new Filtered(null, $locationFilter));
            } else {
                if ($jam->getLocation()->getAdministrativeAreaLevel3()) {
                    $elasticaQuery->addMust(new Match('location.administrative_area_level_3', array('query' => $jam->getLocation()->getAdministrativeAreaLevel3())));
                }
            }
        }

        $boolFilter = new BoolOr();
        $boolFilter->addFilter(new Terms('instruments.instrument.id', $jam->getInstrumentsIds()));
        $elasticaQuery->addMust(new Filtered(null, $boolFilter));

        if (count($jam->getGenresIds()) > 0) {
            $boolFilter = new BoolOr();
            $boolFilter->addFilter(new Terms('genres.genre.id', $jam->getGenresIds()));
            $elasticaQuery->addMust(new Filtered(null, $boolFilter));
        }


        $query = new Query($elasticaQuery);

        $query->setSize(100);
        $query->setFrom(0);

        return $this->elasticUsersFinder->find($query);
    }
}