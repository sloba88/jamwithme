<?php

namespace Jam\CoreBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Jam\CoreBundle\Entity\Shout;

class ShoutCounter {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Shout
     */
    private $shout;

    /**
     * @var bool
     */
    private $canShout = true;

    /**
     * @var int
     */
    private $secondsDifference = 0;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setShout()
    {
        $shout = $this->em->getRepository('JamCoreBundle:Shout')->findOneBy(
            array(
                'creator' => $this->tokenStorage->getToken()->getUser()
            ),
            array(
                'createdAt' => 'DESC'
            )
        );

        if ($shout instanceof Shout) {
            $this->shout = $shout;
            $this->shoutCheck();
        }
    }

    public function getShout()
    {
        return $this->shout;
    }

    public function getCanShout()
    {
        return $this->canShout;
    }

    public function getSecondsDifference()
    {
        return $this->secondsDifference;
    }

    private function shoutCheck()
    {
        $today = new \DateTime('now');
        $this->secondsDifference = (2 * 60 * 60) - ($today->getTimestamp() - $this->shout->getCreatedAt()->getTimestamp());

        if ($this->secondsDifference > (2 * 60 * 60)) {
            $this->canShout = false;
            $this->secondsDifference = 0;
        }
    }
}