<?php

namespace LocalsBest\CommonBundle\ORM;

use Doctrine\ORM\EntityRepository as Repository;
use LocalsBest\UserBundle\Entity\Business;

/**
 * Description of EntityRepository
 *
 * @author Abhinav Kumar <abhinav@nimbleimps.com>
 */
class EntityRepository extends Repository
{
    /**
     * 
     * @param \LocalsBest\UserBundle\Entity\Business $business
     * @param array $criteria
     * @param mixed $orderBy
     * @param type $limit
     * @param type $offset
     * @return array The objects.
     */
    public function findMyBy(Business $business, array $criteria, mixed $orderBy = null, $limit = null, $offset = null)
    {
        if (!isset($criteria['owner'])) {
            $criteria['owner'] = $business;
        }
        
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getDefaultStatus($type = null)
    {
        return $this->getEntityManager()->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('new');
    }

    public function getOpenStatus()
    {
        return $this->getEntityManager()->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('open');
    }

    public function getClosedStatus()
    {
        return $this->getEntityManager()->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('closed');
    }
    
    public function getPendingStatus($type = null)
    {
        return $this->getEntityManager()->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('pending');
    }
    
    public function getActiveStatus($type = null)
    {
        return $this->getEntityManager()->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('completed');
    }
    
    public function getRejectStatus($type = null)
    {
        return $this->getEntityManager()->getRepository('LocalsBest\CommonBundle\Entity\Status')->findOneByStatus('final negative');
    }
    public function save($object)
    {
        if (method_exists($object, 'setUpdated')) {
            $object->setUpdated(time());
        }
        
        if (method_exists($object, 'setCreated') && !$object->getId()) {
            $object->setCreated(time());
        }
        
        if (method_exists($object, 'setStatus') && (!$object->getStatus() || !$object->getStatus() instanceof \LocalsBest\CommonBundle\Entity\Status)) {
            $object->setStatus($this->getDefaultStatus());
        }
        
        $this->getEntityManager()->persist($object);

        $this->getEntityManager()->flush();
    }
    
    public function remove($object)
    {
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush();
    }


    public function findMyObjects($me, $myStaffs = array())
    {
        $qb = $this->createQueryBuilder('d');
        
        $qb->leftJoin('d.shares', 's')
                ->Where('s.user IN (:user)')
                ->orWhere('d.createdBy IN (:user)')
                ->orWhere('d.createdBy = :createdBy')
                ->orWhere('s.user = :createdBy')
                ->setParameter('createdBy', $me)
                ->setParameter('user', $myStaffs);
        
        return $qb->getQuery()->getResult();
    }
}