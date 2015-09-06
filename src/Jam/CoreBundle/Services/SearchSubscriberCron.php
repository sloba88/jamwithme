<?php

namespace Jam\CoreBundle\Services;


use Doctrine\ORM\EntityManager;
use Elastica\Filter\BoolNot;
use Elastica\Filter\GeoDistance;
use Elastica\Filter\MatchAll;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Bool;
use Elastica\Query\Filtered;
use Elastica\Filter\Ids;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Jam\CoreBundle\Entity\Search;

class SearchSubscriberCron {

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TransformedFinder
     */
    private $elasticUserFinder;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setElasticUserFinder(TransformedFinder $finder)
    {
        $this->elasticUserFinder = $finder;
    }

    public function execute()
    {
        $searchEntries = $this->entityManager->getRepository('JamCoreBundle:Search')->findAll();

        if (count($searchEntries) > 0) {
            foreach ($searchEntries as $search) {
                if ($search instanceof Search) {
                    if ($search->getIsSubscribed()) {
                        $searchResults = $this->getElasticSearchResult($search);

                        if (is_array($searchResults) && count($searchResults)) {
                            var_dump($searchResults);exit;
                        }
                    }
                }
            }
        }
    }

    private function getSearchResults(Search $search)
    {
        $userRepo = $this->entityManager->getRepository('JamUserBundle:User');

        $qb = $userRepo->createQueryBuilder('user');

        $qb->add('where', $qb->expr()->in('user.instruments', $search->getInstruments()));
        $qb->add('where', $qb->expr()->in('user.genres', $search->getGenres()));
        $qb->andWhere('user.isTeacher', $search->getIsTeacher());

    }

    /**
     * @param Search $search
     * @return array
     */
    private function getElasticSearchResult(Search $search)
    {

        $elasticaQuery = new \Elastica\Query\MatchAll();

        $instruments = json_decode($search->getInstruments());
        $genres = json_decode($search->getGenres());

        if ($search->getInstruments() !== '') {
            $instrumentsQuery = new Terms('instruments.instrument.id', $instruments);
            $elasticaQuery = new Filtered($elasticaQuery, $instrumentsQuery);
        }


        if ($search->getGenres() !== '') {
            $genresQuery = new Terms('genres.genre.id', $genres);
            $elasticaQuery = new Filtered($elasticaQuery, $genresQuery);
        }


        if ($search->getIsTeacher()) {
            $boolFilter = new Bool();
            $teacherTerm = new Term();
            $teacherTerm->setTerm('isTeacher', '1');
            $boolFilter->addMust($teacherTerm);
            $elasticaQuery = new Filtered($elasticaQuery, $boolFilter);
        }

        if ($search->getCreator()->getLat() && $search->getDistance()) {
            $locationFilter = new GeoDistance(
                'pin',
                array(
                    'lat' => floatval($search->getCreator()->getLat()),
                    'lon' => floatval($search->getCreator()->getLon()),
                ),
                (intval($search->getDistance()) ? intval($search->getDistance()) : '20') . 'km'
            );
            $elasticaQuery = new Filtered($elasticaQuery, $locationFilter);
        }


        $idsFilter =  new Ids();
        $idsFilter->setIds(array($search->getCreator()->getId()));
        $elasticaBool = new BoolNot($idsFilter);
        $elasticaQuery = new Filtered($elasticaQuery, $elasticaBool);

        return $this->elasticUserFinder->find($elasticaQuery);
    }

}