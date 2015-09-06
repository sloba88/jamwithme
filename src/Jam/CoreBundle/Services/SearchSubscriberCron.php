<?php

namespace Jam\CoreBundle\Services;


use Doctrine\ORM\EntityManager;
use Elastica\Filter\BoolNot;
use Elastica\Filter\GeoDistance;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Bool;
use Elastica\Query\Filtered;
use Elastica\Filter\Ids;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Jam\CoreBundle\Entity\Search;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;

class SearchSubscriberCron {

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TransformedFinder
     */
    private $elasticUserFinder;

    /**
     * @var TimedTwigEngine
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TransformedFinder $finder
     */
    public function setElasticUserFinder(TransformedFinder $finder)
    {
        $this->elasticUserFinder = $finder;
    }

    /**
     * @param TimedTwigEngine $twig
     */
    public function setTwig(TimedTwigEngine $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function checkMailParams($mailUser, $mailPassword)
    {
        if ($mailUser === null || $mailPassword === null) {
            throw new \InvalidArgumentException('Email user and password not configured');
        }
    }

    /**
     * Execute cron this method
     */
    public function execute()
    {
        $searchEntries = $this->entityManager->getRepository('JamCoreBundle:Search')->findBy(array('isSubscribed' => true));

        if (count($searchEntries) > 0) {
            $emailCounter = 0;
            foreach ($searchEntries as $search) {
                if ($search instanceof Search) {
                    $searchResults = $this->getElasticSearchResult($search);
                    if($this->searchHasChanges($searchResults, $search)) {

                        $this->sendEmail($searchResults, $search);

                        $search->setUsers($this->formUserIdArray($searchResults));
                        $this->entityManager->persist($search);
                        $emailCounter++;
                    }
                }
            }
            if ($emailCounter > 0) {
                $this->logger->addInfo('Sent ' .$emailCounter. ' emails for search subscriptions');
            }

            $this->entityManager->flush();
        }
    }

    /**
     * Check if $search object has changes in user id array, compared to $searchResults array
     *
     * @param array $searchResults
     * @param Search $search
     * @return bool
     */
    private function searchHasChanges(array $searchResults, Search $search)
    {
        $changed = (count($searchResults) > 0) ? true : false;

        if ($changed) {
            if (count($search->getUsers()) > 0) {
                if ($search->getSortedIntegerUsers() === $this->formUserIdArray($searchResults)) {
                    $changed = false;
                }
            }
        }

        return $changed;
    }

    /**
     * Return array of integers of user id's sorted numerically
     *
     * @param array $searchResults
     * @return array
     */
    private function formUserIdArray(array $searchResults)
    {
        $results = array();
        if (count($searchResults) > 0) {
            foreach ($searchResults as $user) {
                $results[] = $user->getId();
            }
            sort($results, SORT_NUMERIC);
        }

        return $results;
    }

    /**
     * Get search result(s) based on $search object parameters
     *
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

    /**
     * Send email to subscriber of $search entity
     *
     * @param array $searchResults
     * @param Search $search
     */
    private function sendEmail(array $searchResults, Search $search)
    {
        $emailBody = $this->twig->render('JamCoreBundle:Email:userSearchSubscription.html.twig', array(
            'users' => $searchResults
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('User suggestions')
            ->setFrom('noreply@jamifind.com')
            ->setTo($search->getCreator()->getEmail())
            ->setBody($emailBody, 'text/html');

        $this->mailer->send($message);
    }

}