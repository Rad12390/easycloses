<?php

namespace LocalsBest\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use LocalsBest\CommonBundle\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;

/**
 * VendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VendorRepository extends EntityRepository implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('v')
            ->where('v.username = :username')
            ->setParameter('username', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active user identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }
    
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        switch ($response->getResourceOwner()->getName()) {
            case 'facebook':
                $user = $this->grabFacebookUser($response);
                break;
            case 'linkedin':
                $user = $this->grabLinkedInUser($response);
                break;
            case 'twitter':
                $user = $this->grabTwitterUser($response);
                break;
        }
        
        $this->save($user);
        
        return $this->loadUserByUsername($user->getUsername());
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
    
    public function findMyObjects($me, $myStaffs = array())
    {
        $qb = $this->createQueryBuilder('v');
        
        $qb->orWhere('v.createdBy IN (:user)')
            ->orWhere('v.createdBy = :createdBy')
            ->setParameter('createdBy', $me)
            ->setParameter('user', $myStaffs)
            ->orderBy('v.updated', 'DESC');
        
        
        return $qb->getQuery()->getResult();
    }
    
    public function findMyObjectsByCategory($me, $myStaffs = array(), $limit)
    {
        $qb = $this->createQueryBuilder('v');
        
        $qb->orWhere('v.createdBy IN (:user)')
            ->orWhere('v.createdBy = :createdBy')
            ->setParameter('createdBy', $me)
            ->setParameter('user', $myStaffs)
            ->orderBy('v.category', 'DESC');
    
        $query = $qb->getQuery();
        $query->setMaxResults($limit);
        
        return $query->getResult();
    }
    
    public function findVendorsByCategory($businessType, $limit = null)
    {
        if ($limit === null) {
            $limit = count($this->findAll());
        }
        
        $totalVendors = array();
        
        $goldVendors = $this->vendorsRandom($businessType, Vendor::CATEGORY_GOLD, $limit);
        if($goldVendors) {
            foreach ($goldVendors as $goldVendor) {
                $totalVendors[] = $goldVendor;
            }
        }
        
        $maxSilverCount = $limit - count($totalVendors);
        if (!$maxSilverCount) {
            return $totalVendors;
        }
        
        $silverVendors = $this->vendorsRandom($businessType, Vendor::CATEGORY_SILVER, $maxSilverCount);
        if($silverVendors) {
            foreach ($silverVendors as $silverVendor) {
                $totalVendors[] = $silverVendor;
            }
        }
        
        $maxBronzeCount = $limit - count($totalVendors);
        if (!$maxBronzeCount) {
            return $totalVendors;
        }
        
        $bronzeVendors = $this->vendorsRandom($businessType, Vendor::CATEGORY_BRONZE, $maxBronzeCount);
        if($bronzeVendors) {
            foreach ($bronzeVendors as $bronzeVendor) {
                $totalVendors[] = $bronzeVendor;
            }
        }
        
        $maxFreeCount = $limit - count($totalVendors);
        
        if (!$maxFreeCount) {
            return $totalVendors;
        }
        
        $freeVendors = $this->vendorsRandom($businessType, Vendor::CATEGORY_FREE, $maxFreeCount);
        if($freeVendors) {
            foreach ($freeVendors as $freeVendor) {
                $totalVendors[] = $freeVendor;
            }
        }

        return $totalVendors;
    }
    
    private function vendorsRandom($type,$category,$limit)
    {
        $totalVendors = array();
        
        if ($type === 'all') {
            $vendors = $this->findBy(array('category' => $category));
        } else {
            $vendors = $this->findBy(array('businessType' => $type, 'category' => $category));
        }
        
        $totalVendor = count($vendors);
        
        if ($totalVendor <= 1 || $limit <= 1) {
            return $vendors;
        }
        
        if($totalVendor <= $limit ) {
            $limit = $totalVendor;
        }
        
        $randomKeys = array_rand($vendors, $limit);
        shuffle($randomKeys);
        
        $length = 0;

        for ($i= $length; $i<count($randomKeys); $i++) {
            $totalVendors[] = $vendors[$randomKeys[$i]];
        }

        return $totalVendors;
    }
}
