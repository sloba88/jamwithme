<?php

namespace Jam\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Jam\CoreBundle\Entity\Search;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\TwigBundle\TwigEngine;

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
     * @var TwigEngine
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
     * @var SearchMusicians
     */
    private $musiciansSearch;

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
     * @param TwigEngine $twig
     */
    public function setTwig(TwigEngine $twig)
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

    /**
     * @param $search
     */
    public function setMusiciansSearch(SearchMusicians $search)
    {
        $this->musiciansSearch = $search;
    }


    public function checkMailParams($mailUser, $mailPassword)
    {
        if ($mailUser === null || $mailPassword === null) {
            //throw new \InvalidArgumentException('Email user and password not configured');
        }
    }

    /**
     * Execute cron this method
     */
    public function execute()
    {
        $searchEntries = $this->entityManager->getRepository('JamCoreBundle:Search')->findBy(array('isSubscribed' => true));
        $emailCounter = 0;

        if (count($searchEntries) > 0) {
            foreach ($searchEntries as $search) {
                if ($search instanceof Search) {
                    $searchResults = $this->musiciansSearch->getElasticSearchResult($search);

                    $users = array();
                    //convert compatibility as result to user object
                    foreach ($searchResults AS $s) {
                        if (!in_array($s->getMusician2()->getId(), $search->getUsers())) {
                            array_push($users, $s->getMusician2());
                        }
                    }
                    if (count($users) > 0) {

                        if ($this->sendEmail($users, $search)) {
                            $search->setUsers(array_merge($this->formUserIdArray($users), $search->getUsers()));
                            $this->entityManager->persist($search);
                            $emailCounter++;
                        }
                    }
                }
            }
            if ($emailCounter > 0) {
                $this->logger->addInfo('Sent ' .$emailCounter. ' emails for search subscriptions');
                $this->entityManager->flush();
            }
        }

        return $emailCounter;
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
     * Send email to subscriber of $search entity
     *
     * @param array $searchResults
     * @param Search $search
     */
    private function sendEmail(array $searchResults, Search $search)
    {
        $emailBody = $this->twig->render('JamWebBundle:Email:userSearchSubscription.html.twig', array(
            'users' => $searchResults,
            'subscriptionId' => $search->getId()
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('User suggestions')
            ->setFrom('noreply@jamifind.com')
            ->setSender('Jamifind - bringing musicians together')
            ->setTo($search->getCreator()->getEmail())
            ->setBody($emailBody, 'text/html');

        if ($this->mailer->send($message)) {
            return true;
        }

        return false;
    }

}