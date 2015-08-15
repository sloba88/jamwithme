<?php

namespace Jam\CoreBundle\Entity;


use Doctrine\ORM\EntityRepository;

class ShoutRepository extends EntityRepository {

    public function getTodaysShout()
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.createdAt > :today')
            ->andWhere('s.createdAt < :tomorrow')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('tomorrow', new \DateTime('tomorrow'))
            ->getQuery();

        return $qb->getResult();
    }

}