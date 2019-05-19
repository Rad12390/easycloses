<?php

namespace LocalsBest\MedAppUserBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use LocalsBest\CommonBundle\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;

class UserRepository extends EntityRepository implements UserProviderInterface, OAuthAwareUserProviderInterface, UserLoaderInterface
{
    protected $container;
    
    public function setContainer($container)
    {
        $this->container = $container;
        
        return $this;
    }
    
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.role', 'r')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin LocalsBestUserBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
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
    
    public function save($user)
    {
        if (empty($user->getRole())) {
            $user->setRole($this->getDefaultRole());
        }

        if (empty($user->getAboutMe())) {
            $user->setAboutMe($user->getAboutMe());
        }

        foreach ($user->getContact()->getEmails() as $email) {
            $email->setContact($user->getContact());
        }

        if (!$user->getPrimaryEmail() && !$user->getContact()->getEmails()->isEmpty()) {
            $user->setPrimaryEmail($user->getContact()->getEmails()->first());
        }
        
        if (!$user->getPrimaryPhone() && !$user->getContact()->getPhones()->isEmpty()) {
            $user->setPrimaryPhone($user->getContact()->getPhones()->first());
        }
        
        foreach ($user->getContact()->getPhones() as $phone) {
            $phone->setContact($user->getContact());
        }
        
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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
    
    protected function grabFacebookUser(UserResponseInterface $response)
    {
        $email = $response->getEmail();
        $emailExist = $this->container->get('doctrine')->getRepository('LocalsBestUserBundle:Email')
            ->findOneByEmail($email);
        
        if ($emailExist) {
            return $emailExist->getContact()->getUser();
        }
        $user = new User();
        
        $facebookId = $response->getUsername();
        $returnedResponse = $response->getResponse();
        $firstName  = isset($returnedResponse['first_name']) ? $returnedResponse['first_name'] : '';
        $lastName   = isset($returnedResponse['last_name']) ? $returnedResponse['last_name'] : '';
        $userEmail = new Email();

        $userEmail->setEmail($email);
        
        //Set some wild random pass since its irrelevant, this is Google login
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword(md5(uniqid()), $user->getSalt());

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setUsername($email)
            ->setPassword($password)
            ->setFacebookId($facebookId)
            ->getContact()->addEmail($userEmail);
        
        return $user;
    }
    
    protected function grabLinkedInUser(UserResponseInterface $response)
    {
        $email = $response->getEmail();
        $emailExist = $this->container->get('doctrine')->getRepository('LocalsBestUserBundle:Email')
            ->findOneByEmail($email);
        
        if ($emailExist) {
            return $emailExist->getContact()->getUser();
        }
        
        $user = new User();
        $linkedInId = $response->getUsername();
        $fullName = $response->getRealName();
        
        list($firstName, $lastName) = explode(' ', $fullName, 2);

        $userEmail = new Email();
        $userEmail->setEmail($email);
        
        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setUsername($email)
            ->setPassword('password')
            ->setLinkedInId($linkedInId)
            ->getContact()->addEmail($userEmail);
        
        return $user;
    }
    
    protected function grabTwitterUser(UserResponseInterface $response)
    {
        $twitterId = $response->getUsername();
        
        $userExists = $this->container->get('doctrine')->getRepository('LocalsBestUserBundle:User')
            ->findOneByTwitterId($twitterId);
        
        if ($userExists) {
            return $userExists;
        }
        
        $user = new User();
        $fullName = $response->getRealName();
        $screenName = $response->getNickname();
        list($firstName, $lastName) = explode(' ', $fullName, 2);
        
        //Set some wild random pass since its irrelevant, this is Google login
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword(md5(uniqid()), $user->getSalt());

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPassword($password)
            ->setUsername($screenName)
            ->setTwitterId($twitterId);
        
        return $user;
    }
    
    protected function getDefaultRole()
    {
        return $this->getEntityManager()->getRepository('LocalsBestUserBundle:Role')->findOneByRole('ROLE_CLIENT');
    }

    public function findStaff(User $user, Business $business = null, $matching = [])
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->where('u.id != :id')
            ->setParameter('id', $user->getId())
            ->join('u.role', 'r')
            ->andWhere('r.level <= :level')
            ->andWhere('r.level > :adminLevel')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->setParameter('level', 5)
            ->setParameter('adminLevel', 1)
            ->setParameter('owner', $business->getId());

        return $qb->getQuery()->getResult();
    }

    public function findNonStaff(User $user, Business $business = null, $matching = [])
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->where('u.id != :id')
            ->setParameter('id', $user->getId())
            ->join('u.role', 'r')
            ->andWhere('r.level < :lowLevel')
            ->andWhere('r.level > :highLevel')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->setParameter('lowLevel', 8)
            ->setParameter('highLevel', 5)
            ->setParameter('owner', $business->getId());

        return $qb->getQuery()->getResult();
    }

    public function findClients(User $user, Business $business = null, $matching = [])
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->where('u.id != :id')
            ->setParameter('id', $user->getId())
            ->join('u.role', 'r')
            ->andWhere('r.level = :level')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->andWhere('u.createdBy = :id')
            ->setParameter('level', 8)
            ->setParameter('owner', $business->getId());
        return $qb->getQuery()->getResult();
    }
    
    public function findStaffs(User $user, Business $business = null, $matching = [])
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->where('u.id != :id')->setParameter('id', $user->getId());
        
        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb->join('u.role', 'r')
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('creator', $user->getId());
        } elseif($user->getRole()->getLevel() === 6) {
            $qb->join('u.team', 't')
                ->andWhere('t.leader = :leader')
                ->andWhere('u.team = t.id')
                ->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb->andWhere('r.level >= :level');
            } else {
                $qb->andWhere('r.level > :level');
            }
            $qb->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $user->getBusinesses()->first()->getId());
        }

        if (count($matching)) {
            $expr = $qb->expr()->orX();
            $paramCtr = 1;
            foreach ($matching as $column => $value) {
                $expr->add(' u.' . $column . ' LIKE ?' . $paramCtr);
                $qb->setParameter($paramCtr, '%' . $value . '%');
                $paramCtr++;
            }
            $qb->andWhere($expr);
        }

        return $qb->getQuery()->getResult();
    }

    public function findStaffsArray(User $user)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->select('u.id', 'u.firstName', 'u.lastName')
            ->join('u.role', 'r')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
        ;

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');

        if ($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('creator', $user->getId())
            ;
        } elseif ($user->getRole()->getLevel() == 6) {
            $qb
                ->join('u.team', 't')
                ->orWhere('t.leader = :leader')
                ->setParameter('leader', $user)
            ;
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            if ($user->getRole()->getLevel() == 4) {
                $qb->andWhere('r.level >= :level');
            } else {
                $qb->andWhere('r.level > :level');
            }
            $qb
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $user->getBusinesses()->first()->getId())
            ;
        }

        $qb
            ->orderBy('r.level', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function findAllBusiness(User $user)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')
            ->createQueryBuilder('u')
            ->join('u.role', 'r')
        ;
        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');
        if ($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('creator', $user->getId());
        } elseif($user->getRole()->getLevel() === 6) {
            $qb
                ->join('u.team', 't')
                ->andWhere('t.leader = :leader')
                ->andWhere('u.team = t.id')
                ->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            if ($user->getRole()->getLevel() == 4) {
                $qb->andWhere('r.level >= :level');
            } else {
                $qb->andWhere('r.level > :level');
            }
            $qb
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $user->getBusinesses()->first()->getId());
        }

        $qb
            ->andWhere('r.level < 8')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
        ;
        return $qb->getQuery()->getResult();
    }
    
    public function findUpperStaffs(User $user, Business $business)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->where('u.id != :id')->setParameter('id', $user->getId());
        
        $qb
            ->join('u.role', 'r')
            ->andWhere('r.level < :level')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->setParameter('level', $user->getRole()->getLevel())
            ->setParameter('owner', $business->getId());
        
        return $qb->getQuery()->getResult();
    }

    public function findFullStaffs(User $user, Business $business)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->join('u.role', 'r')
            ->andWhere('r.level <> :level')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->orderBy('u.firstName')
            ->addOrderBy('u.lastName')
            ->setParameter('level', Role::ROLE_CLIENT)
            ->setParameter('owner', $business->getId());

        return $qb->getQuery()->getResult();
    }

    public function findActiveFullStaffs(User $user, Business $business)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->where('u.id != :id')->setParameter('id', $user->getId());

        $qb
            ->join('u.role', 'r')
            ->andWhere('r.level <> :level')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->andWhere('u.deleted IS NULL')
            ->setParameter('level', Role::ROLE_CLIENT)
            ->setParameter('owner', $business->getId());

        return $qb->getQuery()->getResult();
    }

    public function findActiveDocApprovers(Business $business)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->select('e.email')
            ->join('u.role', 'r')
            ->andWhere('r.level <> :level')
            ->innerJoin('u.businesses', 'b')
            ->innerJoin('u.primaryEmail', 'e')
            ->andWhere('b.id = :owner')
            ->andWhere('u.deleted IS NULL')
            ->andWhere('u.isDocumentApprover = 1')
            ->setParameter('level', Role::ROLE_CLIENT)
            ->setParameter('owner', $business->getId());

        return $qb->getQuery()->getResult();
    }
    
    public function findShareContacts($shareContactIds = array())
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb ->innerJoin('u.allContacts', 'c')
            ->where('c.id IN (:contactId)')
            ->setParameter('contactId',$shareContactIds);
            
        return $qb->getQuery()->getResult();
    }

    public function findVendorsForVendor(User $user, $businessOwners, $neededType='all', $isWithConcierge=false)
    {
        $em = $this->getEntityManager(); //get the database manager
        $id= $user->getId();
        if ($businessOwners != '') {
            $sql = "SELECT vendor_user_id FROM new_vendors WHERE user_id IN ( $businessOwners )";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute(['user' => $user->getId()]);
            $results = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            // query to show invitee 
            $sql1 = "SELECT vendor_user_id FROM new_vendors WHERE user_id IN ( $id )";
            $stmt1 = $em->getConnection()->prepare($sql1);
            $stmt1->execute(['user' => $user->getId()]);
            $results1 = $stmt1->fetchAll(\PDO::FETCH_COLUMN);
            
            $results = array_merge($results, explode(', ', $businessOwners));
            $results = array_merge($results,$results1);
           
        }

        if (
            !in_array($user->getBusinesses()->first()->getId(), [179])
            && $isWithConcierge === true
        ) {
            $results[] = 1;
        }

        $results[] = $user->getId();

        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->innerJoin('u.businesses', 'b')->innerJoin('b.types', 't');

        foreach ($user->getBusinesses()->first()->getTypes() as $t_ype) {
            $types[] = $t_ype->getId();
        }

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->in('u.id', $results),
                $qb->expr()->orX(
                    $qb->expr()->notIn('t.id', $types),
                    $qb->expr()->eq('u.id', $user->getId())
                )
            )
        );

        if($neededType != 'all') {
            $qb->andWhere('t.id = :nType')
                ->setParameter('nType', $neededType);
        }

        return $qb->getQuery()->getResult();
    }

    public function dataTableQuery(Business $business, $params, User $currentUser = null)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->addSelect('r.level as HIDDEN level, p.number as HIDDEN number, e.email as HIDDEN email')
            ->join('u.role', 'r')
            ->leftJoin('u.primaryEmail', 'e')
            ->leftJoin('u.primaryPhone', 'p')
        ;

        if ($currentUser !== null && $currentUser->getRole()->getLevel() == Role::ROLE_CLIENT) {
            $qb
                ->andWhere('u.id = :ownerId')
                ->setParameter('ownerId', $business->getOwner()->getId());
        } else {
            $qb
                ->andWhere('r.level <> :level')
                ->setParameter('level', Role::ROLE_CLIENT);
        }

        $qb
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->setParameter('owner', $business->getId());

        if(isset($params['status']) && $params['status']== 'archived') {
            $qb->andWhere('u.deleted IS NOT NULL');
        } else {
            $qb->andWhere('u.deleted IS NULL');
        }

        if($params['order'][0]['column'] == 1) {
            $qb->orderBy('u.firstName', $params['order'][0]['dir']);
            $qb->addOrderBy('u.lastName', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 2) {
            $qb->orderBy('number', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 3) {
            $qb->orderBy('email', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 4) {
            $qb->orderBy('level', $params['order'][0]['dir']);
        } elseif($params['order'][0]['column'] == 5) {
            $qb->orderBy('u.updated', $params['order'][0]['dir']);
        } else {
            $qb->orderBy('u.firstName', 'asc');
            $qb->addOrderBy('u.lastName', 'asc');
        }

        if(!empty($params['search']['value'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        $qb->expr()->concat(
                            'u.firstName',
                            $qb->expr()->concat(
                                $qb->expr()->literal(' '),
                                'u.lastName'
                            )
                        ),
                        ':searchValue'
                    ),
                    $qb->expr()->like(
                        'r.name',
                        ':searchValue'
                    ),
                    $qb->expr()->like(
                        'e.email',
                        ':searchValue'
                    ),
                    $qb->expr()->like(
                        'p.number',
                        ':searchValue'
                    )
                )
            )->setParameter('searchValue', '%' . $params['search']['value'] . '%');

            $filteredResults = count($qb->getQuery()->getArrayResult());
        }

        $qb ->distinct(true);
        $totalResults = count($qb->getQuery()->getArrayResult());
        $qb->setMaxResults($params['length'])->setFirstResult($params['start']);

        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        return [!empty($params['search']['value']) ? $filteredResults : null, $paginator, $totalResults];
    }

    public function getClientsByBusiness(Business $business, $currentUser)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->addSelect('r.level as HIDDEN level')
            ->join('u.role', 'r')
            ->leftJoin('u.properties', 'prop')
            ->leftJoin('u.businesses', 'b')
            ->leftJoin('LocalsBestUserBundle:Job', 'j', 'WITH', 'u.id = j.createdBy')
        ;

        $qb
            ->andWhere('u.deleted IS NULL')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        'b.id = :owner',
                        'r.level = :clientLevel'
                    ),
                    $qb->expr()->andX(
                        'j.vendor = :currentUser'
                    )
                )
            )
        ;

        $qb
            ->setParameter('currentUser', $currentUser)
            ->setParameter('owner', $business->getId())
            ->setParameter('clientLevel', Role::ROLE_CLIENT);
        ;

        $qb->orderBy('u.firstName', 'asc');
        $qb->addOrderBy('u.lastName', 'asc');


        $qb ->distinct(true);

        return $qb->getQuery()->getResult();
    }

    public function dataTableClientsQuery(Business $business, $params, User $currentUser = null)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->addSelect('r.level as HIDDEN level, p.number as HIDDEN number, e.email as HIDDEN email')
            ->join('u.role', 'r')
            ->join('u.primaryEmail', 'e')
            ->join('u.primaryPhone', 'p')
            ->leftJoin('u.properties', 'prop')
            ->leftJoin('u.businesses', 'b')
        ;

        $qb
            ->andWhere('u.deleted IS NULL')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        'b.id = :owner',
                        'r.level = :clientLevel',
                        'u.createdBy = :currentUser'
                    )
                )
            )
        ;

        $qb
            ->setParameter('currentUser', $currentUser)
            ->setParameter('owner', $business->getId())
            ->setParameter('clientLevel', Role::ROLE_CLIENT);
        ;

        if ($currentUser->getId() == 2338) {
            if (isset($params['order']) && $params['order'][0]['column'] == 0) {
                $qb->orderBy('u.firstName', $params['order'][0]['dir']);
                $qb->addOrderBy('u.lastName', $params['order'][0]['dir']);
            } elseif(isset($params['order']) && $params['order'][0]['column'] == 1) {
                $qb->orderBy('u.champion_bin_size', $params['order'][0]['dir']);
            } elseif(isset($params['order']) && $params['order'][0]['column'] == 2) {
                $qb->orderBy('u.champion_frequency', $params['order'][0]['dir']);
            } elseif(isset($params['order']) && $params['order'][0]['column'] == 3) {
                $qb->orderBy('number', $params['order'][0]['dir']);
            }  elseif(isset($params['order']) && $params['order'][0]['column'] == 5) {
                $qb->orderBy('u.updated', isset($params['order'][0]['dir']) ? $params['order'][0]['dir'] : 'desc');
            } else {
                $qb->orderBy('u.firstName', 'asc');
                $qb->addOrderBy('u.lastName', 'asc');
            }
        } else {
            if (isset($params['order']) && $params['order'][0]['column'] == 0) {
                $qb->orderBy('u.firstName', $params['order'][0]['dir']);
                $qb->addOrderBy('u.lastName', $params['order'][0]['dir']);
            } elseif(isset($params['order']) && $params['order'][0]['column'] == 1) {
                $qb->orderBy('number', $params['order'][0]['dir']);
            } elseif(isset($params['order']) && $params['order'][0]['column'] == 2) {
                $qb->orderBy('email', $params['order'][0]['dir']);
            }  elseif(isset($params['order']) && $params['order'][0]['column'] == 3) {
                $qb->orderBy('u.updated', isset($params['order'][0]['dir']) ? $params['order'][0]['dir'] : 'desc');
            } else {
                $qb->orderBy('u.firstName', 'asc');
                $qb->addOrderBy('u.lastName', 'asc');
            }
        }

        if (!empty($params['search']['value'])) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like(
                            $qb->expr()->concat(
                                'u.firstName',
                                $qb->expr()->concat(
                                    $qb->expr()->literal(' '),
                                    'u.lastName'
                                )
                            ),
                            ':searchValue'
                        ),
                        $qb->expr()->like(
                            'r.name',
                            ':searchValue'
                        ),
                        $qb->expr()->like(
                            'e.email',
                            ':searchValue'
                        ),
                        $qb->expr()->like(
                            'p.number',
                            ':searchValue'
                        )
                    )
                )
                ->setParameter('searchValue', '%' . $params['search']['value'] . '%');

            $filteredResults = count($qb->getQuery()->getArrayResult());
        }

        $qb ->distinct(true);

        $totalResults = count($qb->getQuery()->getArrayResult());

        $qb->setMaxResults($params['length'])->setFirstResult($params['start']);

        $paginator = $qb->getQuery()->getResult();

        return [!empty($params['search']['value']) ? $filteredResults : null, $paginator, $totalResults];
    }

    public function isTransactionOwnerInStaff(User $user, User $owner)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->where('u.id != :id')->setParameter('id', $user->getId());

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');

        if($user->getRole()->getRole() === 'ROLE_AGENT') {
            $qb
                ->join('u.role', 'r')
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('creator', $user->getId());
        } elseif($user->getRole()->getLevel() === 6) {
            $qb
                ->join('u.team', 't')
                ->andWhere('t.leader = :leader')
                ->andWhere('u.team = t.id')
                ->setParameter('leader', $user);
        } elseif (!in_array($user->getRole()->getRole(), $role)) {
            $qb->join('u.role', 'r');
            if ($user->getRole()->getLevel() == 4) {
                $qb->andWhere('r.level >= :level');
            } else {
                $qb->andWhere('r.level > :level');
            }
            $qb
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->setParameter('level', $user->getRole()->getLevel())
                ->setParameter('owner', $user->getBusinesses()->first()->getId());
        }
        $qb->andWhere('u.id = :transactionOwner')->setParameter('transactionOwner', $owner->getId());
        $result = $qb->getQuery()->getResult();

        return (count($result) > 0 ? true : false);
    }

    public function getMembersByInfo($filters)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->addSelect('r, b, c, e, p, t')
            ->leftJoin('u.role', 'r')
            ->leftJoin('u.businesses', 'b')
            ->leftJoin('b.address', 'a')
            ->leftJoin('b.types', 't')
            ->leftJoin('u.contact', 'c')
            ->leftJoin('c.emails', 'e')
            ->leftJoin('c.phones', 'p')
            ->leftJoin('u.workingCities', 'w_c')
            ->leftJoin('b.workingStates', 'w_s')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        $qb->expr()->concat(
                            'u.firstName',
                            $qb->expr()->concat(
                                $qb->expr()->literal(' '),
                                'u.lastName'
                            )
                        ),
                        ':query'
                    ),
                    'u.firstName LIKE :query',
                    'u.lastName LIKE :query',
                    'b.name LIKE :query',
                    'p.number LIKE :query',
                    'e.email LIKE :query'
                )
            )
            ->andWhere('u.searchVisible = :visibleStatus')
            ->setParameter('visibleStatus', false)
            ->setParameter('query', '%' . $filters['query'] . '%');

        if($filters['state'] != '') {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        'a.state = :state',
                        'w_s.short_name = :state'
                    )
                )
                ->setParameter('state', $filters['state']);
        }
        if($filters['city'] != '') {
            $qb
                ->andWhere('w_c.id = :city')
                ->setParameter('city', $filters['city'])
            ;
        }
        if($filters['business'] != '' && strlen($filters['business']) >= 3) {
            $qb
                ->andWhere('b.name LIKE :business')
                ->setParameter('business', '%' . $filters['business'] . '%');
        }

        $qb
            ->andWhere('r.level <= 7')
            ->andWhere('r.level >= 4')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function findManagerStaff(Business $business)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->addSelect('r, c, e, p')
            ->join('u.role', 'r')
            ->join('u.contact', 'c')
            ->join('c.emails', 'e')
            ->join('c.phones', 'p')
            ->andWhere('r.level <= :level')
            ->andWhere('r.level > :adminLevel')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->setParameter('level', 5)
            ->setParameter('adminLevel', 1)
            ->setParameter('owner', $business->getId())
        ;

        return $qb->getQuery()->getResult();
    }

    public function findLowerManagerStaffs(Business $business)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');

        $qb
            ->addSelect('r, c, e, p')
            ->join('u.role', 'r')
            ->join('u.contact', 'c')
            ->join('c.emails', 'e')
            ->join('c.phones', 'p')
            ->andWhere('r.level <= :level')
            ->andWhere('r.level >= :teamLevel')
            ->innerJoin('u.businesses', 'b')
            ->andWhere('b.id = :owner')
            ->orderBy('r.level', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->setParameter('level', 7)
            ->setParameter('teamLevel', 6)
            ->setParameter('owner', $business->getId())
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function getStaffArray($businessId)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->addSelect('r')
            ->leftJoin('u.businesses', 'b')
            ->leftJoin('u.role', 'r')
            ->where('u.deleted IS NULL')
            ->andWhere('b.id = :businessId')
            ->andWhere('r.level >= 4')
            ->andWhere('r.level <= 7')
            ->setParameter('businessId', $businessId)
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    public function inStaff(User $needed, User $whoseStaff)
    {
        $em = $this->getEntityManager(); //get the database manager
        $qb = $em->getRepository('LocalsBest\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb
            ->select('u.id')
            ->where('u.id != :id')
            ->setParameter('id', $whoseStaff->getId())
        ;

        $role = array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC');

        if ($whoseStaff->getRole()->getRole() === 'ROLE_AGENT') {
            $qb->join('u.role', 'r')
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->setParameter('level', $whoseStaff->getRole()->getLevel())
                ->setParameter('creator', $whoseStaff->getId());
        } elseif ($whoseStaff->getRole()->getLevel() === 6) {
            $qb->join('u.team', 't')
                ->andWhere('t.leader = :leader')
                ->andWhere('u.team = t.id')
                ->setParameter('leader', $whoseStaff);
        } elseif (!in_array($whoseStaff->getRole()->getRole(), $role)) {
            $qb->join('u.role', 'r');
            if ($whoseStaff->getRole()->getLevel() == 4) {
                $qb->andWhere('r.level >= :level');
            } else {
                $qb->andWhere('r.level > :level');
            }
            $qb
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->setParameter('level', $whoseStaff->getRole()->getLevel())
                ->setParameter('owner', $whoseStaff->getBusinesses()->first()->getId())
            ;
        }

        $qb
            ->andWhere('u.id = :neededUser')
            ->setParameter('neededUser', $needed->getId())
        ;

        $result = $qb->getQuery()->getArrayResult();

        return (count($result) > 0 ? true : false);
    }

    /**
     * Get array of users staff IDs
     *
     * @param User $user
     *
     * @return array
     */
    public function findStaffIds(User $user)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u.id')
        ;

        $roleLevel = array(1, 2);
        $level = $user->getRole()->getLevel();

        if ($level == 7) {
            $qb
                ->join('u.role', 'r')
                ->andWhere('r.level > :level')
                ->andWhere('u.createdBy = :creator')
                ->setParameter('level', $level)
                ->setParameter('creator', $user->getId())
            ;
        } elseif ($level == 6) {
            $qb
                ->join('u.team', 't')
                ->andWhere('t.leader = :leader')
                ->andWhere('u.team = t.id')
                ->setParameter('leader', $user)
            ;
        } elseif (!in_array($level, $roleLevel)) {
            $qb->join('u.role', 'r');

            if ($level == 4) {
                $qb->andWhere('r.level >= :level');
            } else {
                $qb->andWhere('r.level > :level');
            }

            $qb
                ->innerJoin('u.businesses', 'b')
                ->andWhere('b.id = :owner')
                ->setParameter('level', $level)
                ->setParameter('owner', $user->getBusinesses()->first()->getId())
            ;
        }

        $result = $qb->getQuery()->getArrayResult();

        return array_column($result, 'id');
    }

    public function getWideStaffArray($businessId)
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->select('u.id', 'u.firstName', 'u.lastName')
            ->leftJoin('u.businesses', 'b')
            ->leftJoin('u.role', 'r')
            ->where('u.deleted IS NULL')
            ->andWhere('b.id = :businessId')
            ->andWhere('r.level >= 4')
            ->andWhere('r.level <= 7')
            ->setParameter('businessId', $businessId)
            ->orderBy('r.level', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
        ;

        return $qb->getQuery()->getArrayResult();
    }
}