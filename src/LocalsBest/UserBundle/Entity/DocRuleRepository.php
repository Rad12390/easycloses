<?php

namespace LocalsBest\UserBundle\Entity;

use LocalsBest\CommonBundle\ORM\EntityRepository;


class DocRuleRepository extends EntityRepository
{
    public function findForNRD($business)
    {
        return $this->createQueryBuilder('dr')
            ->select('dr.documentName, dr.creating, dr.status')
            ->where('dr.business = :business' )
            ->orderBy('dr.documentName', 'asc')
            ->setParameter('business', $business)
            ->distinct(true)
            ->getQuery()
            ->getArrayResult();
    }

}
