<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PaymentSetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentSetRepository extends EntityRepository
{
    public function findByAddon($product, $type)
    {
        $qb = $this->createQueryBuilder('ps');

        $qb
            ->join('ps.paymentRows', 'pr')
            ->where('pr.productType = :type')
            ->andWhere('pr.product = :product')
            ->setParameter('type', $type)
            ->setParameter('product', $product)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
