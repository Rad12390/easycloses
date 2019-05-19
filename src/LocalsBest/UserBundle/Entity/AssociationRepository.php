<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AssociationRepository
 */
class AssociationRepository extends EntityRepository
{
    public function findAssociationsForBusiness(Business $business)
    {
        $qb2  = $this->_em->createQueryBuilder()
            ->select('IDENTITY(ar.association)')
            ->from('LocalsBest\UserBundle\Entity\AssociationRow', 'ar')
            ->where('ar.business = :business');

        $qb = $this->createQueryBuilder('a');
        $qb
            ->where(
                $qb->expr()->notIn(
                    'a.id',
                    $qb2->getDQL()
                )
            )
            ->orderBy('a.title', 'ASC')
            ->setParameter('business', $business);

        $query  = $qb->getQuery();
        return $query->getResult();
    }

    public function findAssociationsForUser(User $user, Business $business)
    {
        $qb3  = $this->_em->createQueryBuilder()
            ->select('IDENTITY(ar2.association)')
            ->from('LocalsBest\UserBundle\Entity\AssociationRow', 'ar2');
        $qb3->where('ar2.user = :user');

        $qb2  = $this->_em->createQueryBuilder()
            ->select('IDENTITY(ar1.association)')
            ->from('LocalsBest\UserBundle\Entity\AssociationRow', 'ar1')
            ->where('ar1.business = :business');

        $qb  = $this->createQueryBuilder('a');
        $qb
            ->where(
                $qb->expr()->in(
                    'a.id',
                    $qb2->getDQL()
                )
            )
            ->andWhere($qb->expr()->notIn('a.id', $qb3->getDQL()))
            ->orderBy('a.title', 'ASC')
            ->setParameter('business', $business)
            ->setParameter('user', $user);

        $query  = $qb->getQuery();
        return $query->getResult();
    }
}
