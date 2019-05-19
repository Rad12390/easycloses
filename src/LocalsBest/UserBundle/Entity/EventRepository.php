<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use LocalsBest\CommonBundle\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
    
    public function findMyObjects($me, $myStaffs = array())//, $statuses = array())
    {
        $qb = $this->createQueryBuilder('e');
        $qb ->leftJoin('e.shares', 's')
            ->leftJoin('e.assignedTo', 'a')
            ->where('e.createdBy = :createdBy OR s.user = :createdBy OR s.user IN (:user) OR a = :createdBy OR a IN (:user)')
            ->setParameter('createdBy', $me)
            ->setParameter('user', $myStaffs)
            ->orderBy('e.created', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
    public function findMyEvents($me, $objectId, $object, $myStaffs = [])
    {
        $qb = $this->createQueryBuilder('e')->leftJoin('e.shares', 's');
        
        $qb->where('e.createdBy = :createdBy OR e.createdBy IN(:mystaffs) OR s.user = :createdBy OR s.user IN(:mystaffs)')
            ->andWhere('e.'.$object.'   = :objectId')
            ->setParameter('objectId', $objectId)
            ->setParameter('createdBy', $me)
            ->setParameter('mystaffs', $myStaffs);
        
        return $qb->getQuery()->getResult();
    }

    public function findEventsForContactOrUSer($objectId, $object)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->where('e.' . $object . ' = :objectId')
            ->setParameter('objectId', $objectId);

        return $qb->getQuery()->getResult();
    }

    public function findEventsTransaction($objectId, $object)
    {
        $qb = $this->createQueryBuilder('e')->leftJoin('e.shares', 's');

        $qb->where('e.'.$object.'   = :objectId')
            ->setParameter('objectId', $objectId);

        return $qb->getQuery()->getResult();
    }

    public function findAjaxEvents($start, $end, $user)
    {
        $qb0 = $this->_em->getRepository('LocalsBest\UserBundle\Entity\User')
            ->createQueryBuilder('u')
            ->select('u.id')
            ->where('u.id != :id')
            ->setParameter('id', $user->getId())
        ;

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');
        $business = $user->getBusinesses()->first();

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0->join('u.role', 'r')
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->andWhere('u.deleted IS NULL')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('creator', $user)
            ;
        } elseif($user->getRole()->getLevel() === 6) {
            $qb0->join('u.team', 'te')
                ->andWhere('te.leader = :leader')
                ->andWhere('u.team = te.id')
                ->andWhere('u.deleted IS NULL')
                ->setParameter('leader', $user)
            ;
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb0->andWhere('r.level >= :level');
            } else {
                $qb0->andWhere('r.level > :level');
            }
            $qb0
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->andWhere('u.deleted IS NULL')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $business);
            ;
        }

        $u_ids = $qb0->getQuery()->getArrayResult();

        $ids = [];
        foreach ($u_ids as $value) {
            $ids[] = $value['id'];
        }

        $qb = $this->createQueryBuilder('e');
        $qb
            ->addSelect('usr')
            ->leftJoin('e.shares', 's')
            ->leftJoin('e.assignedTo', 'a')
            ->leftJoin('e.user', 'usr')
            ->where('e.createdBy = :createdBy')
            ->orWhere(
                $qb->expr()->in('e.createdBy', ':ids')
            )
            ->orWhere('s.user = :createdBy')
            ->orWhere(
                $qb->expr()->in('s.user', ':ids')
            )
            ->orWhere('a = :createdBy')
            ->orWhere(
                $qb->expr()->in('a', ':ids')
            )
            ->andWhere('e.time BETWEEN :start AND :end')
            ->setParameter('createdBy', $user)
            ->setParameter('ids', $ids)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('e.time', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    public function dataTableQuery($user, $params)
    {
        $qb0 = $this->_em->getRepository('LocalsBest\UserBundle\Entity\User')
            ->createQueryBuilder('u')
        ;
        if ($user->getRole()->getLevel() != 1) {
            $qb0
                ->where('u.id != :id')
            ;
        }

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');
        $business = $user->getBusinesses()->first();

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0->join('u.role', 'r')
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->andWhere('u.deleted IS NULL');
        } elseif($user->getRole()->getLevel() === 6) {
            $qb0->join('u.team', 'te')
                ->andWhere('te.leader = :leader')
                ->andWhere('u.team = te.id')
                ->andWhere('u.deleted IS NULL');
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb0->andWhere('r.level >= :level');
            } else {
                $qb0->andWhere('r.level > :level');
            }
            $qb0
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->andWhere('u.deleted IS NULL');
        }

        $qb = $this->createQueryBuilder('e');


        $qb
            ->addSelect('s.status as HIDDEN status')
            ->leftJoin('e.status', 's')
            ->where($qb->expr()->in('e.createdBy', $qb0->getDQL()))
            ->orWhere('e.createdBy = :me')
            ->andWhere('e.time IS NOT NULL')
            ->andWhere('e.deleted IS NULL')
        ;

        $qb
            ->setParameter('me', $user)
        ;

        if ($user->getRole()->getLevel() != 1) {
            $qb
                ->setParameter('id', $user->getId())
            ;
        }


        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('creator', $user)
            ;
        } elseif($user->getRole()->getLevel() === 6) {
            $qb->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $business)
            ;
        }

        if(!empty($params['search']['value'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        's.status',
                        ':searchValue'
                    ),

                    $qb->expr()->like(
                        'e.title',
                        ':searchValue'
                    ),

                    $qb->expr()->like(
                        "DATE_FORMAT(e.time, '%m/%d/%Y %H:%i')",
                        ':searchValue'
                    ),

                    $qb->expr()->like(
                        "DATE_FORMAT(FROM_UNIXTIME(e.created), '%m/%d/%Y %H:%i')",
                        ':searchValue'
                    ),

                    $qb->expr()->like(
                        'e.type',
                        ':searchValue'
                    ),

                    $qb->expr()->like(
                        $qb->expr()->concat(
                            $qb->expr()->concat(
                                $qb->expr()->concat(
                                    'COALESCE(a1.street, COALESCE(a2.street, ja.street))',
                                    $qb->expr()->literal(', ')
                                ),
                                $qb->expr()->concat(
                                    'COALESCE(a1.city, COALESCE(a2.city, ja.city))',
                                    $qb->expr()->literal(', ')
                                )
                            ),
                            $qb->expr()->concat(
                                $qb->expr()->concat(
                                    'COALESCE(a1.state, COALESCE(a2.state, ja.state))',
                                    $qb->expr()->literal(', ')
                                ),
                                'COALESCE(a1.zip, COALESCE(a2.zip, ja.zip))'
                            )
                        ),
                        ':searchValue'
                    )
                )
            )
            ->setParameter('searchValue', '%' . $params['search']['value'] . '%');

            $filteredResults = count($qb->getQuery()->getArrayResult());
        }

        $qb->distinct(true);

        $qbClone = clone $qb;

        $qbClone->select('COUNT(e.id)');

        $totalResults = $qbClone->getQuery()->getSingleScalarResult();

        if ($params['order'][0]['column'] == 1) {
            $qb->orderBy('s.status', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 2) {
            $qb
                ->orderBy('street', $params['order'][0]['dir'])
                ->addOrderBy('city', $params['order'][0]['dir'])
                ->addOrderBy('state', $params['order'][0]['dir'])
                ->addOrderBy('zip', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 3) {
            $qb->orderBy('e.time', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 4) {
            $qb->orderBy('e.title', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 5) {
            $qb->orderBy('e.type', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 7) {
            $qb->orderBy('e.created', $params['order'][0]['dir']);
        } else {
            $qb->orderBy('e.updated', $params['order'][0]['dir']);
        }

        $qb
            ->setMaxResults($params['length'])
            ->setFirstResult($params['start'])
        ;

        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        return [!empty($params['search']['value']) ? $filteredResults : null, $paginator, $totalResults];
    }
}