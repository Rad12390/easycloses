<?php

namespace LocalsBest\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

/**
 * Class DocsForApprove
 *
 * @package LocalsBest\UserBundle\Services
 */
class contactRequest
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
    public function getUnreadCount()
    {
        // Get current User
        $thisUser = $this->container->get('security.token_storage')->getToken()->getUser();
        $entities_request = $this->em->getRepository('LocalsBestShopBundle:SkuContactUs')->getUnreadCountAllVendorRequests($thisUser);
        $counter= count($entities_request);
        
        // Return count of unread contact us request
        return $counter > 0 ? '<span class="badge badge-warning">' . $counter . '</span>' : '';
    }
}