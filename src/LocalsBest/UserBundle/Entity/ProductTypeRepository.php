<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductTypeRepository extends EntityRepository
{
    public function getProductsForCart($ids)
    {
        $qb = $this->createQueryBuilder('pt');

        $qb
            ->addSelect('p, i')
            ->where('pt.id in (:ids)')
            ->leftJoin('pt.product', 'p')
            ->leftJoin('p.images', 'i')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
