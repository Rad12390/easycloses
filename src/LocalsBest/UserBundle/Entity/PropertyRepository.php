<?php

namespace LocalsBest\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PropertyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PropertyRepository extends EntityRepository
{
    public function save(Property $property)
    {
        $this->getEntityManager()->persist($property);
        
        $this->getEntityManager()->flush();
    }
}