<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\ShopBundle\Entity\Package;
/**
 * QuotesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuotesRepository extends EntityRepository
{
    
    public function findAllOrderByPackageName($vendorid,$param)
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.packageId','p')
            ->where('q.vendorid = :vendorid')
            ->setParameter('vendorid', $vendorid)
            ->orderBy('p.title', $param['order'][0]['dir'])
            ->setFirstResult($param['start'])
            ->setMaxResults($param['length'])
            ->getQuery() 
            ->getResult();
    }
    
    public function findAllOrderByUserName($vendorid,$param)
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.userid','u')
            ->where('q.vendorid = :vendorid')
            ->setParameter('vendorid', $vendorid)
            ->orderBy('u.firstName', $param['order'][0]['dir'])
            ->orderBy('u.lastName', $param['order'][0]['dir'])
            ->setFirstResult($param['start'])
            ->setMaxResults($param['length'])
            ->getQuery() 
            ->getResult();
    }
    
    public function findAllOrderByCreated($vendorid,$param)
    {
        return $this->createQueryBuilder('q')
            ->where('q.vendorid = :vendorid')
            ->setParameter('vendorid', $vendorid)
            ->orderBy('q.createdAt', $param['order'][0]['dir'])
            ->setFirstResult($param['start'])
            ->setMaxResults($param['length'])
            ->getQuery() 
            ->getResult();
    }
    
    public function findAllOrderByQuote($vendorid,$param)
    {
        return $this->createQueryBuilder('q')
            ->where('q.vendorid = :vendorid')
            ->setParameter('vendorid', $vendorid)
            ->orderBy('q.quote', $param['order'][0]['dir'])
            ->setFirstResult($param['start'])
            ->setMaxResults($param['length'])
            ->getQuery() 
            ->getResult();
    }
    
    public function findAllOrderByUpdated($vendorid,$param)
    {
        return $this->createQueryBuilder('q')
            ->where('q.vendorid = :vendorid')
            ->setParameter('vendorid', $vendorid)
            ->orderBy('q.updatedAt', $param['order'][0]['dir'])
            ->setFirstResult($param['start'])
            ->setMaxResults($param['length'])
            ->getQuery() 
            ->getResult();
    }
    
    public function findAllOrderBySearch($params)
    {
        
        $qb = $this->createQueryBuilder('q');
        
        
        $qb ->leftJoin('q.userid', 'u')
            ->leftJoin('q.packageId', 'p')
            ->where('q.vendorid = 1')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        'p.title',
                        ':searchValue'
                    ),
                    
                    $qb->expr()->like(
                        'u.firstName',
                        ':searchValue'
                    ),
                        
                    $qb->expr()->like(
                        'u.lastName',
                        ':searchValue'
                    ),
                        
                    $qb->expr()->like(
                        'q.quote',
                        ':searchValue'
                    ),
                        
                    $qb->expr()->like(
                        'q.createdAt',
                        ':searchValue'
                    ),
                        
                    $qb->expr()->like(
                        'q.updatedAt',
                        ':searchValue'
                    )
                )
            )
            ->setParameter('searchValue', '%' . $params['search']['value'] . '%');
            
           return  $qb->setFirstResult($params['start'])
                        ->setMaxResults($params['length'])
                        ->getQuery() 
                        ->getResult();
    }
}
