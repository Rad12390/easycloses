<?php

namespace LocalsBest\MedAppUserBundle\Services;

use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

/**
 * Class ResourceCounter
 *
 * @package LocalsBest\UserBundle\Services
 */
class ResourceCounter
{
    protected $em;

    private $container;

    public function __construct(Container $container, EntityManager $manager)
    {
        $this->container = $container;
        $this->em = $manager;
    }

    /**
     * Display count of Transactions
     *
     * @param User $user
     *
     * @return string
     */
    public function transactions($user)
    {
        // Get User Business
        $business = $user->getBusinesses()[0];

        // Get Doc Types Count
        $result = $this->em->getRepository('LocalsBestUserBundle:Transaction')
            ->getCount($user, $business);
        $count = array_first($result)[1];

        // Return count of Doc Types for Approve
        return $count > 0 ? '<span class="badge badge-warning">' . $count . '</span>' : '';
    }

    /**
     * Display count of Jobs
     *
     * @param User $user
     *
     * @return string
     */
    public function jobs(User $user)
    {
        // Get Doc Types Count
        $result = $this->em->getRepository('LocalsBestUserBundle:Job')
            ->getCount($user);

        $count = array_first($result)[1];

        // Return count of Doc Types for Approve
        return '<span class="badge badge-warning">' . $count . '</span>';
    }
}