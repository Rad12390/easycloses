<?php

namespace LocalsBest\CommonBundle\Entity;

use LocalsBest\CommonBundle\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function findMyTags($me, $objectId, $object)
    {
        $qb = $this->createQueryBuilder('t');
        
        $qb->where('t.createdBy         = :createdBy')
            ->andWhere('t.objectType = :object')
            ->andWhere('t.objectId   = :objectId')
            ->setParameter('objectId', $objectId)
            ->setParameter('object', $object)
            ->setParameter('createdBy', $me);
        
        return $qb->getQuery()->getResult();
    }

    public function findTagsForContactOrUser($objectType, $objectId)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.objectType = :object')
            ->andWhere('t.objectId   = :objectId')
            ->andWhere(
                $qb->expr()->isNull('t.deleted')
            )
            ->setParameter('object', $objectType)
            ->setParameter('objectId', $objectId->getId());
        
        return $qb->getQuery()->getResult();
    }

    public function getUniqTags($user, $type)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t.tag')
            ->where('t.objectType = :type')
            ->andWhere('t.createdBy = :user')
            ->groupBy('t.tag')
            ->distinct(true)
            ->setParameter('type', $type)
            ->setParameter('user', $user);

        return $qb->getQuery()->getArrayResult();
    }
}