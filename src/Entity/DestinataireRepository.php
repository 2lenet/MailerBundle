<?php

namespace Lle\MailBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DestinataireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DestinataireRepository extends EntityRepository
{
    public function countOuverture($mail)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d)')
            ->where('d.dateOuvert is not null')
            ->andWhere('d.mail = :mail')->setParameter('mail', $mail)
            ->getQuery()->getSingleScalarResult();
    }

    public function countClique($mail)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d)')
            ->where('d.url is not null')
            ->andWhere('d.mail = :mail')->setParameter('mail', $mail)
            ->getQuery()->getSingleScalarResult();
    }
}
