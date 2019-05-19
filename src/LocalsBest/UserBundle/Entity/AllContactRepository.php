<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use LocalsBest\CommonBundle\ORM\EntityRepository;

/**
 * AllContactRepository
 */
class AllContactRepository extends EntityRepository
{
    public function findMyObjects($me, $myStaffs = array(), $matching = [])
    {
        $qb = $this->createQueryBuilder('c');
        
        $qb->where('c.createdBy = :me')
            ->setParameter('me', $me)
            ->orderBy('c.updated', 'DESC');
        
        if (count($matching)) {
            $expr = $qb->expr()->orX();
            $paramCtr = 1;
            foreach ($matching as $column => $value) {
                $expr->add(' c.' . $column . ' LIKE ?' . $paramCtr);
                $qb->setParameter($paramCtr, '%' . $value . '%');
                $paramCtr++;
            }
            $qb->andWhere($expr);
        }
        
        return $qb->getQuery()->getArrayResult();
    }

    public function findAllContacts(User $user, $filter = null)
    {
        $qb0 = $this->_em->getRepository('LocalsBestUserBundle:User')
            ->createQueryBuilder('u')->select('u.id');

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');
        $business = $user->getBusinesses()->first();

        if ($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0
                ->join('u.role', 'r')
                ->where('u.id = :user_id');
        } elseif ($user->getRole()->getLevel() === 6) {
            $qb0
                ->join('u.team', 'te')
                ->where('te.leader = :leader')
                ->andWhere('u.team = te.id');
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb0->where('r.level >= :level');
            } else {
                $qb0->where('r.level > :level');
            }
            $qb0
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner');
        }

        if ($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0->setParameter('user_id', $user->getId());
        } elseif ($user->getRole()->getLevel() === 6) {
            $qb0->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $business);
        }
        $staff = $qb0->getQuery()->getScalarResult();
        $staff_array = array_map('current', $staff);
        $staff_array[] = $user->getId();

        $qb = $this->createQueryBuilder('c');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('c.assign_to', $staff_array),
                $qb->expr()->in('c.generated_by', $staff_array),
                $qb->expr()->in('c.createdBy', $staff_array)
            )
        )
        ->orderBy('c.is_pending', 'DESC')
        ->addOrderBy('c.updated', 'DESC');

        if ($filter !== null && $filter != "all") {
            if ($filter == "members") {
                $qb->andWhere('c.user is not null');
            } elseif($filter == "contacts") {
                $qb->andWhere('c.user is null');
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function checkContact(User $user, AllContact $contact)
    {
        $qb0 = $this->_em->getRepository('LocalsBestUserBundle:User')
            ->createQueryBuilder('u')->select('u.id');

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');
        $business = $user->getBusinesses()->first();

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0
                ->join('u.role', 'r')
                ->where('u.id = :user_id');
        } elseif($user->getRole()->getLevel() === 6) {
            $qb0
                ->join('u.team', 'te')
                ->where('te.leader = :leader')
                ->andWhere('u.team = te.id');
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb0->where('r.level >= :level');
            } else {
                $qb0->where('r.level > :level');
            }
            $qb0
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner');
        }

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0->setParameter('user_id', $user->getId());
        } elseif($user->getRole()->getLevel() === 6) {
            $qb0->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $business);
        }

        $staff = $qb0->getQuery()->getScalarResult();
        $staff_array = array_map('current', $staff);
        $staff_array[] = $user->getId();

        $qb = $this->createQueryBuilder('c');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('c.assign_to', $staff_array),
                $qb->expr()->in('c.generated_by', $staff_array),
                $qb->expr()->in('c.createdBy', $staff_array)
            ))
            ->andWhere('c.id = :contact_id')
            ->orderBy('c.is_pending', 'DESC')
            ->addOrderBy('c.updated', 'DESC')
            ->setParameter('contact_id', $contact->getId());

        $result = $qb->getQuery()->getOneOrNullResult();

        if($result === null){
            return false;
        } else {
            return true;
        }
    }

    public function dataTableQuery($user, $params = null)
    {
        $qb0 = $this->_em->getRepository('LocalsBestUserBundle:User')
            ->createQueryBuilder('u')->select('u.id');

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');
        $business = $user->getBusinesses()->first();

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0
                ->join('u.role', 'r')
                ->where('u.id = :user_id');
        } elseif($user->getRole()->getLevel() === 6) {
            $qb0
                ->join('u.team', 'te')
                ->where('te.leader = :leader')
                ->andWhere('u.team = te.id');
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb0->where('r.level >= :level');
            } else {
                $qb0->where('r.level > :level');
            }
            $qb0
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner');
        }

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb0->setParameter('user_id', $user->getId());
        } elseif($user->getRole()->getLevel() === 6) {
            $qb0->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb0
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $business);
        }
        $staff = $qb0->getQuery()->getScalarResult();
        $staff_array = array_map('current', $staff);
        $staff_array[] = $user->getId();

        $f_name_string = 'COALESCE(as_to.firstName, COALESCE(g_by.firstName, c_by.firstName))';
        $l_name_string = 'COALESCE(as_to.lastName, COALESCE(g_by.lastName, c_by.lastName))';

        $qb = $this->createQueryBuilder('c');
        $qb->select('c', $f_name_string . ' as firstName', $l_name_string . ' as lastName');
        $qb->leftJoin('LocalsBestUserBundle:User', 'as_to', "WITH", 'c.assign_to = as_to.id');
        $qb->leftJoin('LocalsBestUserBundle:User', 'g_by', "WITH", 'c.generated_by = g_by.id');
        $qb->leftJoin('LocalsBestUserBundle:User', 'c_by', "WITH", 'c.createdBy = c_by.id');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('as_to.id', $staff_array),
                $qb->expr()->in('g_by.id', $staff_array),
                $qb->expr()->in('c_by.id', $staff_array)
            ));

        $columns = $params['columns'];
//        if($params['search']['value'] == ''
//            && ($user->getRole()->getLevel() != 7 && $columns[1]['search']['value'] == '')
//            && (($user->getRole()->getLevel() == 7 && $columns[1]['search']['value'] == '') || ($user->getRole()->getLevel() != 7 && $columns[2]['search']['value'] == ''))
//            && (($user->getRole()->getLevel() == 7 && $columns[2]['search']['value'] == '') || ($user->getRole()->getLevel() != 7 && $columns[3]['search']['value'] == ''))
//            && (($user->getRole()->getLevel() == 7 && $columns[3]['search']['value'] == '') || ($user->getRole()->getLevel() != 7 && $columns[4]['search']['value'] == ''))
//            && (($user->getRole()->getLevel() == 7 && $columns[4]['search']['value'] == '') || ($user->getRole()->getLevel() != 7 && $columns[5]['search']['value'] == ''))
//            && (($user->getRole()->getLevel() == 7 && $columns[5]['search']['value'] == '') || ($user->getRole()->getLevel() != 7 && $columns[6]['search']['value'] == ''))
//            && (($user->getRole()->getLevel() == 7 && $columns[6]['search']['value'] == '') || ($user->getRole()->getLevel() != 7 && $columns[7]['search']['value'] == ''))
//        ) {
//            $qb->andWhere($qb->expr()->eq('c.is_active', true));
//        }
        if( $params['filter'] !== null && $params['filter'] != "all" ) {
            if($params['filter'] == "members") {
                $qb->andWhere('c.user is not null');
            } elseif($params['filter'] == "contacts") {
                $qb->andWhere('c.user is null');
            }
        }

        if( isset($params['filter_status'])  && $params['filter_status'] != "all" ) {
            if($params['filter_status'] == "open") {
                $qb->andWhere($qb->expr()->eq('c.is_active', 1));
            } elseif($params['filter_status'] == "closed") {
                $qb->andWhere($qb->expr()->eq('c.is_active', 0));
            }
        }

        if (!isset($params['order'])) {
            $params['order'][0]['column'] = 999;
        }

        if(
            ($user->getRole()->getLevel() == 7 && $params['order'][0]['column'] == 1)
            || ($user->getRole()->getLevel() != 7 && $params['order'][0]['column'] == 2)
        ) {
            $qb->orderBy('c.firstName', $params['order'][0]['dir'])->addOrderBy('c.lastName', $params['order'][0]['dir']);
        } elseif (
            ($user->getRole()->getLevel() == 7 && $params['order'][0]['column'] == 2)
            || ($user->getRole()->getLevel() != 7 && $params['order'][0]['column'] == 3)
        ) {
            $qb->orderBy('c.number', $params['order'][0]['dir']);
        } elseif($user->getRole()->getLevel() != 7 && $params['order'][0]['column'] == 1) {
            $qb->orderBy('firstName', $params['order'][0]['dir'])->addOrderBy('lastName', $params['order'][0]['dir']);
        } elseif (
            ($user->getRole()->getLevel() == 7 && $params['order'][0]['column'] == 3)
            || ($user->getRole()->getLevel() != 7 && $params['order'][0]['column'] == 4)
        ) {
            $qb->orderBy('c.email', $params['order'][0]['dir']);
        } elseif(
            ($user->getRole()->getLevel() == 7 && $params['order'][0]['column'] == 4)
            || ($user->getRole()->getLevel() != 7 && $params['order'][0]['column'] == 5)
        ) {
            $qb->orderBy('c.category', $params['order'][0]['dir']);
        } else {
            $qb->orderBy('c.is_pending', 'DESC')->addOrderBy('c.updated', 'DESC');
        }

        if($user->getRole()->getLevel() != 7 && $columns[0]['search']['value'] !== '') {
            $qb->andWhere(
                $qb->expr()->in(
                    $qb->expr()->concat(
                        $f_name_string,
                        $qb->expr()->concat(
                            $qb->expr()->literal(' '),
                            $l_name_string
                        )
                    ),
                    ':agentList'
                )
            )->setParameter('agentList', array_map('trim', explode('|', $columns[0]['search']['value'])));
        }

        if(
            ($user->getRole()->getLevel() == 7 && $columns[0]['search']['value'] !== '')
            || ($user->getRole()->getLevel() != 7 && $columns[1]['search']['value'] !== '')
        ) {
            $qb->andWhere(
                $qb->expr()->like(
                    $qb->expr()->concat(
                        'c.firstName',
                        $qb->expr()->concat(
                            $qb->expr()->literal(' '),
                            'c.lastName'
                        )
                    ),
                    ':value2'
                )
            )->setParameter('value2', '%' . ($user->getRole()->getLevel() == 7 ? $columns[0]['search']['value'] : $columns[1]['search']['value']) . '%');
        }
        if(($user->getRole()->getLevel() == 7 && $columns[1]['search']['value'] !== '') ||  ($user->getRole()->getLevel() != 7 && $columns[2]['search']['value'] !== '')) {
            $qb->andWhere(
                $qb->expr()->like(
                    'c.number',
                    ':value3'
                )
            )->setParameter('value3', '%' . ($user->getRole()->getLevel() == 7 ? $columns[1]['search']['value'] : $columns[2]['search']['value']) . '%');
        }
        if(($user->getRole()->getLevel() == 7 && $columns[2]['search']['value'] !== '') || ($user->getRole()->getLevel() != 7 && $columns[3]['search']['value'] !== '')) {
            $qb->andWhere(
                $qb->expr()->like(
                    'c.email',
                    ':value4'
                )
            )->setParameter('value4', '%' . ($user->getRole()->getLevel() == 7 ? $columns[2]['search']['value'] : $columns[3]['search']['value']) . '%');
        }
        if(($user->getRole()->getLevel() == 7 && $columns[3]['search']['value'] !== '') || ($user->getRole()->getLevel() != 7 && $columns[4]['search']['value'] !== '')) {
            $qb->andWhere(
                $qb->expr()->like(
                    'c.category',
                    ':value5'
                )
            )->setParameter('value5', '%' . ($user->getRole()->getLevel() == 7 ? $columns[3]['search']['value'] : $columns[4]['search']['value']) . '%');
        }
        if(($user->getRole()->getLevel() == 7 && $columns[4]['search']['value'] !== '') || ($user->getRole()->getLevel() != 7 && $columns[5]['search']['value'] !== '')) {
            $qb->innerJoin('c.tags', 't');
            $qb->andWhere(
                $qb->expr()->in(
                    't.tag',
                    ':value6'
                )
            )->setParameter('value6',  explode('|', ($user->getRole()->getLevel() == 7 ? $columns[4]['search']['value'] : $columns[5]['search']['value'])));
        }
        if(($user->getRole()->getLevel() == 7 && $columns[5]['search']['value'] !== '') || ($user->getRole()->getLevel() != 7 && $columns[6]['search']['value'] !== '')) {
            $qb->innerJoin('c.notes', 'n');
            $qb->andWhere(
                $qb->expr()->like(
                    'n.note',
                    ':value7'
                )
            )->setParameter('value7', '%' . ($user->getRole()->getLevel() == 7 ? $columns[5]['search']['value'] : $columns[6]['search']['value']) . '%');
        }
        $filteredResults = 0;

        if(!empty($params['search']['value'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        $qb->expr()->concat(
                            $f_name_string,
                            $qb->expr()->concat(
                                $qb->expr()->literal(' '),
                                $l_name_string
                            )
                        ),
                        ':searchValue'
                    ),
                    $qb->expr()->like(
                        $qb->expr()->concat(
                            'c.firstName',
                            $qb->expr()->concat(
                                $qb->expr()->literal(' '),
                                'c.lastName'
                            )
                        ),
                        ':searchValue'
                    ),
                    $qb->expr()->like('c.category', ':searchValue'),
                    $qb->expr()->like('c.email', ':searchValue'),
                    $qb->expr()->like('c.number', ':searchValue')
                )
            )->setParameter('searchValue', '%' . $params['search']['value'] . '%');

            $filteredResults = count($qb->getQuery()->getArrayResult());
        }
        $qb ->distinct(true);
        $totalResults = count($qb->getQuery()->getArrayResult());
        $qb->setMaxResults($params['length'])->setFirstResult($params['start']);

        $paginator = new Paginator($qb, $fetchJoinCollection = false);
        return [!empty($params['search']['value']) ? $filteredResults : null, $paginator, $totalResults];
    }

    public function findForPrint($ids)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->addSelect('FIELD(c.id,' . implode(',', $ids) . ') as HIDDEN field');
        $qb->where(
            $qb->expr()->in(
                'c.id',
                $ids
            )
        )
        ->orderBy(
            'field'
        );
        
        return $qb->getQuery()->getResult();
    }
}
