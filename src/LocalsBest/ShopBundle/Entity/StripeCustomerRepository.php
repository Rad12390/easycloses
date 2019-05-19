<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;


/**
 * SkuRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StripeCustomerRepository extends EntityRepository
{
    public function getLBCustomer($params)
    {
        $qb = $this->createQueryBuilder('c_s');

        $qb
            ->andWhere('c_s.user = :user')
            ->andWhere('c_s.wholesaler is null')
            ->setParameter('user', $params['user'])
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
