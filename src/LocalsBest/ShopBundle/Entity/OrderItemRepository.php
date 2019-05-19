<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\User;

/**
 * OrderItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderItemRepository extends EntityRepository
{
    public function findOrdered(User $user)
    {
        $qb = $this->createQueryBuilder('o_i');

        $qb
            ->addSelect('o')
            ->leftJoin('o_i.sku', 's')
            ->leftJoin('s.combo', 'c')
            ->leftJoin('s.package', 'p')
            ->join('o_i.order', 'o')
            ->join('o.payments', 'payments')
            ->where(
                $qb->expr()->orX(
                    'p.createdBy = :user',
                    'c.createdBy = :user'
                )
            )
            ->andWhere('o.status = :status')
            ->orderBy('o.createdAt', 'desc')
            ->setParameters([
                'user' => $user,
                'status' => 'paid'
            ])
        ;

        return $qb->getQuery()->getResult();
    }

    public function getByPlugin($params)
    {
        $qb = $this->createQueryBuilder('o_i');

        $qb
            ->join('o_i.order', 'o')
            ->join('o.payments', 'payments')
            ->join('payments.userPlugins', 'user_plugins')
            ->where('o.user = :user')
            ->andWhere('o.status = :status')
            ->andWhere('payments.token is not null')
            ->andWhere('user_plugins.plugin = :plugin')
            ->orderBy('o.createdAt', 'desc')
            ->setParameters([
                'user' => $params['user'],
                'status' => 'paid',
                'plugin' => $params['plugin'],
            ]);

        return $qb->getQuery()->getResult();
    }
}
