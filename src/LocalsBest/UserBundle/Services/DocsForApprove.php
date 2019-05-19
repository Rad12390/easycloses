<?php

namespace LocalsBest\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

/**
 * Class DocsForApprove
 *
 * @package LocalsBest\UserBundle\Services
 */
class DocsForApprove
{
    protected $em;

    private $container;

    public function __construct(Container $container, EntityManager $manager)
    {
        $this->container = $container;
        $this->em = $manager;
    }

    /**
     * Display count of Doc Types for Approve
     *
     * @return string
     */
    public function getCount()
    {
        // Get current User
        $thisUser = $this->container->get('security.token_storage')->getToken()->getUser();
        // Get User Business
        $business = $thisUser->getBusinesses()[0];
        // According to User Role Level choose User Entity
        $user = $thisUser->getRole()->getLevel() < 6 ? $business->getOwner() : $thisUser;
        // Disable Soft Delete Filter
        $this->em->getFilters()->enable('softdeleteable');
        $this->em->getFilters()->disable('softdeleteable');

        // Get Doc Types Count
        $documentTypes = $this->em->getRepository('LocalsBestUserBundle:DocumentType')
            ->findDocumentsForApprovalService(
                $user,
                null,
                null,
                true
            );

        $this->em->getFilters()->enable('softdeleteable')->disableForEntity('LocalsBest\UserBundle\Entity\Document');
        $this->em->getFilters()->enable('softdeleteable')->disableForEntity('LocalsBest\UserBundle\Entity\User');
        // Return count of Doc Types for Approve
        return $documentTypes > 0 ? '<span class="badge badge-warning">' . $documentTypes . '</span>' : '';
    }
}