<?php

namespace LocalsBest\RestBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class UserController extends Controller
{
    static $entityClass = 'LocalsBest\\UserBundle\\Entity\\User';
    
    /**
     * @Route("/", name="rest_user_getall")
     */
    public function getAllAction(Request $request) 
    {
        $query = $request->query->get('query');
        $type = $request->query->get('type');
        $users = array();
        
        if(!empty($type)) {
            $business = $this->getRepository('LocalsBestUserBundle:IndustryType')->find($type);
            $category = $business->getName();
            $qb = $this->getRepository('LocalsBestUserBundle:Vendor')->createQueryBuilder('v');

            $qb->join('v.businessType', 'b')
                ->where('b.name = :type')
                ->andWhere('v.businessName LIKE :query OR v.contactName LIKE :query')
                ->setParameter('type', $category)
                ->setParameter('query', $query . '%')
                ->orderBy('v.category', 'DESC');
            $query = $qb->getQuery();
            //$query->setMaxResults(4);
            $vendors = $query->getResult();
            $jsonPayload = array();
            $uploadHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

            foreach ($vendors as $vendor) {
                $jsonPayload[] = array(
                    'id'                => $vendor->getId(),
                    'contactName'       => $vendor->getContactName(),
                    'email'             => $vendor->getEmail(),
                    'phone'             => $vendor->getNumber(),
                    'businessName'      => $vendor->getBusinessName(),
                    'type'              => $vendor->getBusinessType()->getName(),
                    'profilepic'        => $vendor->getFileName() ? $uploadHelper->asset($vendor, 'file') : 'http://www.placehold.it/64x64/EFEFEF/AAAAAA&text=no+image',
                );
            }
        } else {
            $qb = $this->getRepository('LocalsBestUserBundle:User')->createQueryBuilder('u');
            $qb->join('u.role', 'r')
                    ->where('u.id != :id')
                    //->andWhere('r.level >= :level')
                    ->andWhere('u.username LIKE :query OR u.firstName LIKE :query OR u.lastName LIKE :query')
                    //->setParameter('level', $this->getUser()->getRole()->getLevel())
                    ->setParameter('id', $this->getUser()->getId())
                    ->setParameter('query', $query . '%');

            $users = $qb->getQuery()->getResult();

            if(count($users) > 0) {
                foreach ($users as $key => $user) {
                    if(count($user->getBusinesses()) > 0) {
                        if ($user->getBusinesses()->first()->getId() !== $this->getBusiness()->getId()) {
                            unset($users[$key]);
                        }
                    } else {
                        unset($users[$key]);
                    }
                }
            }

            $jsonPayload = array();
            $uploadHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

            foreach ($users as $user) {
                $jsonPayload[] = array(
                    'id'            => $user->getId(),
                    'username'      => $user->getUsername(),
                    'fullname'      => $user->getFirstname() . ' ' . $user->getLastname(),
                    'profilepic'    => $user->getFileName() ? $uploadHelper->asset($user, 'file') : 'http://www.placehold.it/64x64/EFEFEF/AAAAAA&text=no+image',
                    // Change this so it shows role for the current business
                    'role'          => $user->getRole()->getName()
                );
            }
        }
        
        $defaultParams = array(
            'Content-type'  => 'application/json'
        );
        return new Response(json_encode($jsonPayload), 200, $defaultParams);
    }
}