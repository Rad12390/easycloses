<?php

namespace LocalsBest\MedAppUserBundle\Services;

use LocalsBest\ShopBundle\Entity\OrderItem;
use LocalsBest\ShopBundle\Entity\UserPlugin;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

/**
 * Class Checker
 *
 * @package LocalsBest\UserBundle\Services
 */
class Checker
{
    protected $em;

    private $container;

    public function __construct(Container $container, EntityManager $manager) {
        $this->container = $container;
        $this->em = $manager;
    }

    /**
     * Have User needed Product or Not
     *
     * @param null $slug
     * @param User|null $user
     *
     * @return bool
     */
    public function productBySlug($slug = null, User $user = null)
    {
        // $slug can not be empty
        if($slug === null) {
            return false;
        }

        $securityToken = $this->container->get('security.token_storage')->getToken();

        // If $user is empty than user current User
        if($user === null) {
            if($securityToken === null) {
                return false;
            }

            $thisUser = $securityToken->getUser();

            if($thisUser === null) {
                return false;
            }
        } else {
            $thisUser = $user;
        }

        // Uses query to check relation between User and Product
        $result = $this->em->getRepository('LocalsBestUserBundle:PaymentRow')->findProductForUser($thisUser, $slug);

        return $result;
    }

    /**
     * Function look for addon by user and product bundle
     *
     * @param null|string $name
     * @param User|null $user
     * @return bool
     */
    public function forAddon($name = null, User $user = null)
    {
        // Check User
        if ($user->getRole()->getLevel() == 1) {
            return true;
        }

        // Check Hardcoded User
        if (
            in_array($user->getId(), [68, 70, 1983, 1177, 1785, 1406, 1799, 1470, 927, 1513, 1750, 792, 884, 1758, 1898, 1850, 827, 794, 1427, 3046, 1147, 1659, 2749, 719, 1265, 1739])
            && strpos($name, 'social posting') !== false
        ) {
            return true;
        }

        if ($name === null) {
            return false;
        }

        $securityToken = $this->container->get('security.token_storage')->getToken();

        if ($user === null) {
            if ($securityToken === null) {
                return false;
            }

            $thisUser = $securityToken->getUser();

            if ($thisUser === null) {
                return false;
            }
        } else {
            $thisUser = $user;
        }

        // Get User Business
        $businessId = $this->container->get('session')->get('current_business');
        if ($businessId === null) {
            $business = $user->getBusinesses()[0];
        } else {
            $business = $this->em->getRepository('LocalsBestUserBundle:Business')->find($businessId);
        }

        // Check addon for business by products modules
        $result = $this->em->getRepository('LocalsBestUserBundle:Product')->checkAddon($name, $business);
       
        if ($result !== false) {
            if (
                $result->getAddonType() == 'managers&assistants'
                && in_array($thisUser->getRole()->getLevel(), [4, 5])
            ) {
                return true;
            } elseif (
                $result->getAddonType() == 'managers-agents'
                && in_array($thisUser->getRole()->getLevel(), [4, 5, 6, 7])
            ) {
                return true;
            } elseif (
                $result->getAddonType() == 'managers-clients'
                && in_array($thisUser->getRole()->getLevel(), [4, 5, 6, 7, 8])
            ) {
                return true;
            } elseif (
                $result->getAddonType() == 'clients'
                && in_array($thisUser->getRole()->getLevel(), [8])
            ) {
                return true;
            }
        }

        // Look for addon using payment history
        $result = $this->em->getRepository('LocalsBestUserBundle:PaymentRow')->findAddonForUser($thisUser, $name);
        
        if ($result === false && in_array($thisUser->getRole()->getLevel(), [4, 5])) {
            $owner = $business->getOwner();

            $result = $this->em->getRepository('LocalsBestUserBundle:PaymentRow')
                ->findAddonForUser([$thisUser, $owner], $name, 'managers&assistants');
             
        }

        if ($result === false && in_array($thisUser->getRole()->getLevel(), [4, 5, 6, 7])) {
            $owner = $business->getOwner();

            $result = $this->em->getRepository('LocalsBestUserBundle:PaymentRow')
                ->findAddonForUser([$thisUser, $owner], $name, 'managers-agents');
        }

        if ($result === false && in_array($thisUser->getRole()->getLevel(), [4, 5, 6, 7, 8])) {
            $owner = $business->getOwner();

            $result = $this->em->getRepository('LocalsBestUserBundle:PaymentRow')
                ->findAddonForUser([$thisUser, $owner], $name, 'managers-clients');
        }

        if ($result === false && $thisUser->getRole()->getLevel() == 8) {
            $createdBy = $thisUser->getCreatedBy();

            $result = $this->em->getRepository('LocalsBestUserBundle:PaymentRow')
                ->findAddonForUser($createdBy, $name, 'clients');
        }

        if ($result === false) {
            $result = $this->checkOrders($name, $thisUser);
        }
        
//
        if ($result === false) {
            $result = $this->pluginSystem($name, $thisUser);
        }
       
        // Return response
        return $result;
    }


    /**
     * Function look for plugin by user and sku
     *
     * @param null|string $pluginSlug
     * @param User|null $user
     *
     * @return bool
     */
    public function forPlugin($pluginSlug = null, User $user = null)
    {
        // Check User
        if ($user->getRole()->getLevel() == 1) {
            return true;
        }

        if ($pluginSlug === null) {
            return false;
        }

        $securityToken = $this->container->get('security.token_storage')->getToken();

        if ($user === null) {
            if ($securityToken === null) {
                return false;
            }

            $thisUser = $securityToken->getUser();

            if ($thisUser === null) {
                return false;
            }
        } else {
            $thisUser = $user;
        }

        // Get User Business
        $businessId = $this->container->get('session')->get('current_business');
        if ($businessId === null) {
            $business = $user->getBusinesses()[0];
        } else {
            $business = $this->em->getRepository('LocalsBestUserBundle:Business')->find($businessId);
        }

        // Look for plugin
        $plugin = $this->em->getRepository('LocalsBestUserBundle:Plugin')->findOneBy(['slug' => $pluginSlug]);

        $result = $this->em->getRepository('LocalsBestShopBundle:UserOrder')->findPluginForUser($thisUser, $plugin);

        // Return response
        return $result;
    }

    public function pluginSystem($pluginSlug = null, User $user = null)
    {
        $pluginSlug = str_replace(' ', '-', $pluginSlug);
       
        // Look for plugin
        $plugin = $this->em->getRepository('LocalsBestUserBundle:Plugin')->findOneBy(['slug' => $pluginSlug]);
        
        $pluginSets = $this->em->getRepository('LocalsBestShopBundle:UserPlugin')->findBy(
            ['user' => $user, 'plugin' => $plugin],
            ['createdAt' => 'DESC']
        );

        if (count($pluginSets) == 0) {
            return false;
        }

        /** @var UserPlugin $lastSet */
        $lastSet = array_first($pluginSets);

        if ($lastSet->getUsesLimit() != -1 && $lastSet->getUsesLimit() <= $lastSet->getUses()) {
            return false;
        }

        // Return response
        return true;
    }

    /**
     * @param null $pluginSlug
     * @param User|null $user
     *
     * @return bool
     */
    private function checkOrders($pluginSlug = null, User $user = null)
    {
        $pluginSlug = str_replace('-', ' ', $pluginSlug);
        // Look for plugin
        $plugin = $this->em->getRepository('LocalsBestUserBundle:Plugin')->findOneBy(['slug' => $pluginSlug]);

        $orderItems = $this->em->getRepository('LocalsBestShopBundle:OrderItem')
            ->getByPlugin(['plugin' => $plugin, 'user' => $user]);
        
        if (count($orderItems) == 0) {
            return false;
        }

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            if (
                $orderItem->getSubscriptionEndedAt() !== null
                && $orderItem->getSubscriptionEndedAt()->getTimestamp() > time()
            ) {
                return true;
            }
        }

        return false;
    }
    
    public function checkShopAccess(){
        $securityToken = $this->container->get('security.token_storage')->getToken();
        $user = $securityToken->getUser();
        $userid= $user->getId();
        $user_array= [30,287,594,501,1558,1701,3396,3427,2251,2248,1];
        $user_role= ['Agent','Assistant Manager','Manager'];
        if(in_array($userid,$user_array)){
            return true;
        }
        else{
            $business = $user->getBusinesses()[0];
            $business_id= $business->getId();
            if($business_id==23 && in_array($user->getRole()->getName(),$user_role)){
                return true;
            }
            return false;
        }
    }
    
//    public function checkEsignCreatorAccess(){
//        $securityToken = $this->container->get('security.context')->getToken();
//        $user = $securityToken->getUser();
//        $userid= $user->getId();
//        $user_array= [30,70];
//        
//        if(in_array($userid,$user_array)){
//            return true;
//        }
//        return false;
//    }
//    
//    public function checkEsignSenderAccess(){
//        $securityToken = $this->container->get('security.context')->getToken();
//        $user = $securityToken->getUser();
//        $userid= $user->getId();
//        $user_array= [30,70,2263,1701,1558,3427,3435,3436];
//        $user_role= ['Agent'];
//        $business = $user->getBusinesses()[0];
//        $business_id= $business->getId();
//        if(in_array($userid,$user_array)){
//            return true;
//        }
//        elseif($business_id==23 && in_array($user->getRole()->getName(),$user_role)){
//            return true;
//        }
//        return false;
//    }
//    
//    public function checkEsignSigneeAccess(){
//        $securityToken = $this->container->get('security.context')->getToken();
//        $user = $securityToken->getUser();
//        $userid= $user->getId();
//        $user_array= [30,70,3434];
//        if(in_array($userid,$user_array)){
//            return true;
//        }
//    }
}