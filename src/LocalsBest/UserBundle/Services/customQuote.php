<?php

namespace LocalsBest\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

/**
 * Class DocsForApprove
 *
 * @package LocalsBest\UserBundle\Services
 */
class customQuote
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
        $vendor_id= $thisUser->getId();
        $counter=0;
        $entities_quotes = $this->em->getRepository('LocalsBestShopBundle:Quotes')->findBy(['vendorid' => $vendor_id]);
        
        foreach($entities_quotes as $entity){
            if($entity->getCreatedAt()->format('Y-m-d H:i:s')==$entity->getUpdatedAt()->format('Y-m-d H:i:s')){
                 $counter++;  
            }
        }
        // Return count of Doc Types for Approve
        return $counter > 0 ? '<span class="badge badge-warning">' . $counter . '</span>' : '';
    }
}