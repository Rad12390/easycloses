<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PaymentRowRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRowRepository extends EntityRepository
{
    public function findProductForUser(User $user, $slug)
    {
        $qb = $this->createQueryBuilder('pr');

        $qb
            ->addSelect('p')
            ->leftJoin('pr.product', 'p')
            ->leftJoin('pr.paymentSet', 'ps')
            ->leftJoin('ps.createdBy', 'u')
            ->where('p.slug = :slug')
            ->andWhere('u.id = :userId')
            ->andWhere('pr.status = :status')
            ->orderBy('pr.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('userId', $user->getId())
            ->setParameter('slug', $slug)
            ->setParameter('status', 'success')
        ;

        $result = $qb->getQuery()->getArrayResult();

        if(count($result) > 0) {
            $item = $result[0];
            // TODO: update condition with subscription end date
            if($item['productType'] == 'counter' && $item['productUses'] > $item['productLimit']) {
                return true;
            }

            if($item['productType'] == 'item') {
                return true;
            }

            if($item['productType'] == 'subscription' && $item['productUses'] > $item['productLimit']) {
                return true;
            }
        }

        return false;
    }

    public function findAddonForUser($users, $name, $type = 'single')
    {
        $qb = $this->createQueryBuilder('pr');

        $qb
            ->addSelect('p')
            ->leftJoin('pr.product', 'p')
            ->leftJoin('pr.paymentSet', 'ps')
            ->where('p.addon_part = :name')
            ->andWhere('p.addon_type = :addonType')
            ->andWhere('pr.status = :status')
            ->orderBy('pr.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('name', $name)
            ->setParameter('addonType', $type)
            ->setParameter('status', 'success')
        ;

        if (is_array($users)) {
            $qb
                ->andWhere('ps.createdBy in (:users)')
                ->setParameter('users', $users)
            ;
        } else {
            $qb
                ->andWhere('ps.createdBy = :userId')
                ->setParameter('userId', $users)
            ;
        }

        $result = $qb->getQuery()->getArrayResult();

        if(count($result) > 0) {
            $item = $result[0];
            // TODO: update condition with subscription end date
            if($item['productType'] == 'counter' && $item['productUses'] > $item['productLimit']) {
                return true;
            }

            if($item['productType'] == 'item') {
                return true;
            }

            if($item['productType'] == 'subscription' && $item['productUses'] > $item['productLimit']) {
                return true;
            }
        }

        return false;
    }

    public function checkTypeForUser($productType, $user)
    {
        $qb = $this->createQueryBuilder('pr');

        $qb
            ->leftJoin('pr.product', 'p')
            ->leftJoin('pr.paymentSet', 'ps')
            ->where('pr.product = :product')
            ->andWhere('ps.createdBy = :user')
            ->setParameter('product', $productType->getProduct())
            ->setParameter('user', $user)
        ;

        return count($qb->getQuery()->getArrayResult()) > 0 ? true : false;
    }
}
