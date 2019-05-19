<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PackageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PackageRepository extends EntityRepository
{
    public function findPackage($user){
        $qb = $this->createQueryBuilder('p');
        if ($user->getRole()->getLevel() > 1) {
            $qb 
                ->where('p.type != :type')
                ->andWhere('p.createdBy = :user')
                ->setParameter('type',7)
                ->setParameter('user',$user)
                ->orderBy('p.id', 'DESC');
        }
        else{
            $qb 
                ->where('p.type != :type')
                ->setParameter('type',7)
                ->orderBy('p.id', 'DESC');
        }
        return $qb->getQuery()->getResult();
    }
}
