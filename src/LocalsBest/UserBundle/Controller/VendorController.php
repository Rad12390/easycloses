<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\UserBundle\Dbal\Types\InviteStatusType;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\BusinessView;
use LocalsBest\UserBundle\Entity\Invite;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Entity\Vendor;
use LocalsBest\UserBundle\Form\AdminVendorType;
use LocalsBest\UserBundle\Form\VendorInviteType;
use LocalsBest\UserBundle\Form\VendorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorController extends SuperController
{
    /**
     * Vendors Summary Page
     * @return array
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $users = array();
            $businesses = $this->getUser()->getBusinesses();
           
           foreach($businesses as $business) {
               $user = $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(array('id' => $business->getOwner()->getid()));
               $users[] = $user;
           }
        } else {
            $users = $this->getStaffs();
        }

        $vendors = $em->getRepository('LocalsBest\UserBundle\Entity\Vendor')->findMyObjects($this->getUser(), $users);
        // Return params to view
        return [
            'vendors' => $vendors
        ];
    }


    /**
     * Create/edit vendor
     *
     * @param Request $request
     * @param null $token
     * @param null $id
     * @return array|RedirectResponse
     * @Template
     */
    public function editAction(Request $request, $token = null, $id = null)
    {
        $business = null;
        
        if ($this->get('session')->get('current_business')) {
            $business = $this->getRepository('LocalsBestUserBundle:Business')->find($this->get('session')->get('current_business'));
        }
        $vendorId = null;
        $em = $this->getDoctrine()->getManager();

        if ($token) {
            // Get Invite by token
            $invite = $em->getRepository('LocalsBest\UserBundle\Entity\Invite')->findOneBy(['token' => $token]);
            
            if (!$invite) {
                throw $this->createNotFoundException('Invalid or Expired token');
            }
            // Get User who create Invite
            $user = $em->getRepository('LocalsBestUserBundle:User')->find($invite->getCreatedBy());
            $decodeToken = base64_decode($invite->getToken());
            $filterToken = explode(':', $decodeToken);
            $inviteTime = $filterToken[0];
            $tokenExpiry = $this->container->getParameter('token_expiry');
            $validTime = (time() - $tokenExpiry);

            if (false && $inviteTime > $validTime) {
                $invite->setStatus(InviteStatusType::EXPIRED);
                $em->persist($invite);
                $em->flush();

                throw $this->createNotFoundException('Invalid or Expired token');
            }
        }   
            
        if ($id) {
            // Get Vendor by ID
            $vendor = $this->findOr404('LocalsBestUserBundle:Vendor', ['id' => $id], 'No such vendor found');
            $vendorId = $vendor->getId();
            if (!$this->canEdit($vendor)) {
                throw $this->createNotFoundException('Job not found');
            }
            if ($this->getUser()->getRole()->getRole() != 'ROLE_NETWORK_MANAGER') {
                $myStaffs = $this->getStaffs();
                if ($vendor->getCreatedBy() != $this->getUser() && !in_array($vendor->getCreatedBy(), $myStaffs)) {
                    throw $this->createNotFoundException('Access Denied');
                }
            }
        } else {
            // Create new Vendor
            $vendor = new Vendor();
            $vendor->setCreatedBy($this->getUser());

            $data = $this->get('request_stack')->getCurrentRequest()->request->get('vendor');
            $password= $this->generatePassword();
            $vendor->setUsername($data['email']);
            $vendor->setCategory(0);
            $vendor->setActive(0);
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($vendor);
            $password = $encoder->encodePassword($password, null);
            $vendor->setPassword($password);
        }
        if ($token !== null && $invite !== null) {
            $vendor->setEmail($invite->getEmail())->setCategory(0);
        }

        if (
            $this->getUser()->getRole()->getRole() === 'ROLE_ADMIN'
            || $this->getUser()->getRole()->getRole() === 'ROLE_CUSTOMER_SERVIC'
        ) {
            $form = $this->createForm(AdminVendorType::class, $vendor);
        } else {
            $form = $this->createForm(VendorType::class, $vendor, [
                'user' => $this->getUser()
            ]);
        }

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $vendorData = $form->getData();
                if ($token !== null && $invite !== null) {
                    // Update Invite
                    $user->addVendor($vendorData);
                    $vendorData->addUser($user);
                    $invite->setToken(null);
                    $invite->setStatus(InviteStatusType::ACCEPTED);
                    $em->persist($invite);
                    $em->flush();
                } elseif ($id === null) {

                }
                if ($id === null) {
                    $time   = time();
                    $randNum = json_encode(['category' => $vendorData->getCategory(), 'businessType' => $vendorData->getBusinessType()->getId()]);
                    $newToken = base64_encode($time . ':' . $randNum);
                    // Create New Envite
                    $invite = new Invite();
                    // Get Role entity for Vendor
                    $role = $em->getRepository('LocalsBestUserBundle:Role')->findOneByName('Vendor');
                    // Set Information for Invite
                    $invite
                        ->setEmail($vendorData->getEmail())
                        ->setIndustryType($vendorData->getBusinessType())
                        ->setRole($role)
                        ->setToken($newToken)
                        ->setCreatedBy($this->getUser())
                        ->setStatus(InviteStatusType::INVITE);
                    $em->getRepository('LocalsBest\UserBundle\Entity\Invite')->save($invite);

                    $messages = 'You have successfully sent an invite to:'. $vendorData->getEmail();
                    // Send Email about this
                    $this->getMailMan()->sendVendorCreateMail($vendor, $newToken, $this->getUser());
                }
                // Show flash message
                $this->addSuccessMessage($messages);
                return $this->redirectToRoute('service_index');
            } 
        }
        // Return params to view
        return [
            'form' => $form->createView(),
            'vendorId' => $vendorId,
            'user' => $this->getUser(),
        ];
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @return string
     */
    private function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
        // Generate string
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        // Return string
        return $result;
    }

    /**
     * Send email with invite
     *
     * @param Request $request
     * @return array|Response
     */
    public function inviteAction(Request $request)
    {
        $level  = $this->getUser()->getRole()->getLevel();

        $em = $this->getDoctrine()->getManager();

        if (!$level) {
            throw $this->createNotFoundException('Access Denied');   
        }
        // Create Vendor Invite Form for Vendor
        $form = $this->createForm(VendorInviteType::class);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // Get requested emails
                $emails = $form->get('emails')->getData();
                
                $emails = explode(',', $emails);
                $emails = array_filter($emails, "trim");
                $emails = array_unique($emails);
                
                $messages = array();
                
                foreach ($emails as $email) {
                    if (empty(trim($email))) {
                        continue;
                    }
                    // check Vendor for existing
                    $vendorExists = $this->getRepository('LocalsBestUserBundle:Vendor')->findOneByEmail($email);
                    if ($vendorExists) {
                        // Send Email about this
                        $this->getMailMan()->sendMail(
                        'Membership Notifications',
                        '@LocalsBestNotification/mails/exist-vendor-invite.html.twig', 
                        array('user' => $this->getUser(), 'vendor' => $vendorExists), 
                        array($email)
                        );
                        $messages[] = $email .'is exists already. Vendor added with you';
                    } else {                
                        $time   = time();
                        $randNum = rand();
                        $token = base64_encode($time . ':' . $randNum);
                        // Create new Invite
                        $invite = new Invite();
                        $role = $this->getRepository('LocalsBestUserBundle:Role')->findOneByName('Vendor');
                        $invite->setEmail($email)
                                ->setRole($role)
                                ->setToken($token)
                                ->setCreatedBy($this->getUser())
                                ->setStatus(InviteStatusType::INVITE);
                        // Save invite
                        $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Invite')->save($invite);
                        // Send Email about this
                        $this->getMailMan()->sendMail(
                            'Invitation to Localsbest.com',
                            '@LocalsBestNotification/mails/vendor-invite.html.twig',
                            array('user' => $this->getUser(),'token' => $invite->getToken()),
                            array($email)
                        );
                        $messages[] = 'your invitation send successfully to '.$email;
                    }
                }
                // Return JSON response
                return new Response(json_encode($messages));
            } 
        }
        // Return params to view
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Get Vendors List
     *
     * @param Request $request
     * @param string $businessType
     * @param int|null $limit
     * @return Response|array
     * @Template
     */
    public function ajaxSelectedAction(Request $request, $businessType, $limit = null) 
    {
        /** @var Business $biz */
        $biz = $this->getUser()->getBusinesses()[0];
        $u = $biz->getOwner();

        $isFromMainPage = (bool)$request->query->get('main_page', false);
        $job = (bool)$request->query->get('job', false);
        
        $isWithConcierge = $this->get('localsbest.checker')->forAddon('concierge service', $this->getUser());

        if (
            $this->getBusiness()->getTypes()->first()->getId() == 23
            || in_array($this->getBusiness()->getId(), [173])
        ) {
            /** @var User $vendors */
            $vendors = $this->getUser()->getVendorsByCategory2($u, $businessType, $isFromMainPage, $isWithConcierge);
        } else {
            $vUsers = [];
            $vUsersRaw = $u->getVendorsWithMe();
            foreach($vUsersRaw as $item) {
                $vUsers[] = $item->getId();
            }
            $em = $this->getDoctrine()->getManager();

            $results = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                ->findVendorsForVendor($u, implode(', ', $vUsers), $businessType, $isWithConcierge);

            $vendors = [];

            $platinumCat = [];
            $goldCat = [];
            $silverCat = [];
            $bronzeCat = [];
            $freeCat = [];

            // Split vendors by categories
            foreach($results as $vendor){
                if ($vendor->getVendorCategory() == 4) {
                    $platinumCat[] = $vendor;
                    continue;
                } elseif ($vendor->getVendorCategory() == 3) {
                    $goldCat[] = $vendor;
                    continue;
                } elseif ($vendor->getVendorCategory() == 2) {
                    $silverCat[] = $vendor;
                    continue;
                } elseif ($vendor->getVendorCategory() == 1) {
                    if ($isFromMainPage === false) {
                        $bronzeCat[] = $vendor;
                    }
                    continue;
                } else {
                    if ($isFromMainPage === false) {
                        $freeCat[] = $vendor;
                    }
                    continue;
                }
            }

            shuffle($platinumCat);
            shuffle($goldCat);
            shuffle($silverCat);
            shuffle($bronzeCat);
            shuffle($freeCat);
            // Merge vendors to one list
            $vendors = array_merge($vendors, $platinumCat, $goldCat, $silverCat, $bronzeCat, $freeCat);
        }
        
        // Return params to view
        if(!$job){
            return [
                'vendors'           => $vendors,
                'isFromMainPage'    => $isFromMainPage,
            ];
        }
        else{
            $vendors_array=[];
            $vendorsId_array=[];
           foreach($vendors as $vendor){
                foreach($vendor->getBusinesses() as $vendorBusiness){
                    array_push($vendors_array,$vendorBusiness->getName());
                    array_push($vendorsId_array,$vendorBusiness->getId());
                }
            }
            return new JsonResponse(array('vendors' => $vendors_array, 'vendorsId_array' => $vendorsId_array));
        }
    }

    /**
     * Get vendors by business type
     *
     * @param $businessType
     * @return array|JsonResponse
     * @Template
     */
    public function sliderAction(Request $request, $businessType)
    {
        $biz = $this->getUser()->getBusinesses()[0];
        $u = $biz->getOwner();

        $vendors = $this->getUser()->getVendorsByCategory2($u, $businessType);

        if ($request->request->has('vendor_id')) {
            foreach ($vendors as $vendor) {
                if($vendor->getId() != $request->request->get('vendor_id')) {
                    $vendors->removeElement($vendor);
                }
            }
        }

        if (count($vendors) == 0) {
            // Return JSON response
            return new JsonResponse(array(
                'message' => 'No matching vendors found'
            ), 404);
        }
        // Return params to view
        return array(
            'vendors'   => $vendors,
            'isVendorPrefilled' => $request->request->has('vendor_id'),
        );
    }

    /**
     * Get Vendor List by Category
     *
     * @param Request $request
     * @param $businessType
     * @return JsonResponse
     */
    public function jsonAction(Request $request, $businessType)
    {
        $query = $request->query->get('query');
        $isWithConcierge = $this->get('localsbest.checker')->forAddon('concierge service', $this->getUser());
        // echo "<pre>";
		 //print_r($this->getUser());
		// echo "</pre>";
        //$vendors = $this->getRepository('LocalsBest\UserBundle\Entity\Vendor')->findVendorsByCategory($businessType);
        $vendors = $this->getUser()->getVendorsByCategory($businessType, false, $isWithConcierge);
		//echo "fdffdsafdf  : ". $query;
       /*  echo "isWithConcierge  : ". $isWithConcierge;
		print_r($vendors);
		die; */
        // Check vendors for condition
        foreach ($vendors as $key => $vendor) {
            if ($query !== '') {
                if (
                    strpos(strtolower($vendor->getFirstname()), strtolower($query)) !== false
                    || strpos(strtolower($vendor->getLastname()), strtolower($query)) !== false
                    || strpos(strtolower($vendor->getBusinesses()->first()->getName()), strtolower($query)) !== false
                ) {
                    continue;
                } else {
                    unset($vendors[$key]);
                    continue;
                }
            }
        }

        if (empty($vendors)) {
            return new JsonResponse(array('message' => 'No matching vendors found'), 404);
        }
        // Prepare response array
        $payload = [];
        foreach ($vendors as $vendor) {
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            $payload[] = [
                'id'            => $vendor->getId(),
                'businessName'  => $vendor->getBusinesses()->first()->getName(),
                'contactName'   => $vendor->getFirstname() . ' ' . $vendor->getLastname(),
                'profilepic'    => $helper->asset($vendor, 'file'),
                'email'         => $vendor->getPrimaryEmail()->getEmail(),
                'phone'         => str_replace(['(', ')', '-', ' ', '.'], '', $vendor->getPrimaryPhone()->getNumber()),
            ];
        }
        
        return new JsonResponse($payload);
    }

    /**
     * Get Vendors by Object entity
     *
     * @param string $type
     * @param string $checkType
     * @param string $element
     * @return array|JsonResponse
     * @Template
     */
    public function ajaxCheckedAction($type, $checkType, $element)
    {
        $users = [];
        $em = $this->getDoctrine()->getManager();

        if ($element !== 'empty') {
            if ($checkType === 'primaryEmail' || $checkType === 'primaryPhone') {
                if ($checkType === 'primaryEmail') {
                    $object = 'LocalsBestUserBundle:Email';
                    $objectType = 'findOneByEmail';
                } else {
                    $object = 'LocalsBestUserBundle:Phone';
                    $objectType = 'findByNumber';
                }
                // Get Object Entity
                $objectExists = $this->getRepository($object)->$objectType($element);
                if ($objectExists) {
                    // Get Users by Object
                    $users = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                        ->findBy([$checkType => $objectExists]);
                }
            } elseif ($checkType === 'businessName' || $checkType === 'contactName') {
                $object = 'LocalsBestUserBundle:Business';
                if ($checkType === 'businessName') {
                    $objectType = 'findOneByName';
                } else {
                    $objectType = 'findOneByContactName';
                }
                // Get Object Entity
                $business = $this->getRepository($object)->$objectType($element);
                if ($business !== null) {
                    // get business staff
                    $users = $business->getStaffs();
                }
            }
        }

        foreach ($users as $key => $user) {
            //Check for users that have own business
            if (count($user->getBusinesses()) == 0) {
                unset($users[$key]);
                continue;
            }
            //Check for users business that have industry type
            if (count($user->getBusinesses()->first()->getTypes()) == 0) {
                unset($users[$key]);
                continue;
            }
            //Check for users that not a clients
            if ($user->getRole()->getRole() === 'ROLE_CLIENT') {
                unset($users[$key]);
                continue;
            }
        }

        if (empty($users)) {
            // Return JSON Response
            return new JsonResponse(array(
                'message' => ''
            ), 404);
        }

        return array(
            'users'  => $users,
            'type'  => $type
        );
    }

    /**
     * Attach vendor to user
     *
     * @param int $vendorId
     * @return RedirectResponse
     */
    public function addToUserAction($vendorId)
    {
        $em = $this->getDoctrine()->getManager();

        // Get User-vendor by ID
        /** @var Vendor $vendor */
        $vendor = $this->findOr404('LocalsBestUserBundle:User', array('id' => $vendorId), 'No such user found');
        $user = $this->getUser();

        if ($user->getAllVendors()->contains($vendor)) {
            // Show flash message
            $this->addFlash('warning', 'You already contains this vendor');
            // Redirect User
            return $this->redirectToRoute('service_index');
        }
        // Attach Vendor to current User
        $user->setMyVendors($vendor);
        $vendor->setVendorsWithMe($user);

        // Send Email about this
        $this->getMailMan()->sendMail(
            'Membership Notifications',
            '@LocalsBestNotification/mails/exist-vendor-invite.html.twig',
            array('user' => $this->getUser(), 'vendor' => $vendor),
            array($vendor->getPrimaryEmail()->getEmail())
        );
        // Update DB
        $em->flush();

        // Redirect User
        return $this->redirectToRoute('service_index');
    }

    /**
     * Detach vendor from user
     *
     * @param int $vendorId
     * @return RedirectResponse
     */
    public function removeVendorAction($vendorId)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Vendor by ID
        $vendor = $this->findOr404('LocalsBestUserBundle:User', array('id' => $vendorId), 'No such user found');
        $user = $this->getUser();
        // Remove Vendor from users list
        $user->getMyVendors()->removeElement($vendor);
        $vendor->getVendorsWithMe()->removeElement($user);
        // Save changes
        $em->persist($vendor);
        $em->flush();

        return $this->redirectToRoute('service_index');
    }

    /**
     * Get Vendor Bio
     *
     * @param Request $request
     * @param int $vendorId
     * @return Response
     * @Template
     */
    public function bioAjaxAction(Request $request, $vendorId)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Vendor by ID
        $vendor = $em->getRepository('LocalsBestUserBundle:User')->find($vendorId);

        $user = $this->getUser();
        $businessId = $vendor->getBusinesses()[0]->getId();
        $em->clear();

        $businessView = new BusinessView();
        $businessView->setBusiness($em->getReference('LocalsBestUserBundle:Business', $businessId));
        // Update Vendor Bio View counter by 1
        $businessView->setIp($request->getClientIp());
        $businessView->setInfo($request->headers->get('User-Agent'));

        if ($user !== null) {
            $businessView->setUser($em->getReference('LocalsBestUserBundle:User', $user->getId()));
        }

        $em->persist($businessView);
        // Update DB
        $em->flush();

        $vendor = $em->getRepository('LocalsBestUserBundle:User')->find($vendorId);
        // render view
        return [
            'vendor'  => $vendor,
        ];
    }

    /**
     * Display block with business vendors
     *
     * @param string $businessSlug
     * @return Response
     */
    public function directoryAction($businessSlug)
    {
        $em = $this->getDoctrine()->getManager();

        // Get business by slug
        /** @var Business $business */
        $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

        if ($business === null) {
            throw $this->createNotFoundException();
        }
        $vendors = $business->getOwner()->getVendorsByCategory2($business->getOwner());
        // Get available categories
        $categories = $this->getDoctrine()->getRepository('LocalsBestUserBundle:IndustryType')
            ->createQueryBuilder('i')
            ->where('i.id IN (:categories)')
            ->setParameter('categories', $business->getOwner()->getAbleVendorCategories())
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();

        $categories = $this->getDoctrine()->getRepository('LocalsBestUserBundle:IndustryType')
            ->createQueryBuilder('i')
            ->where('i.id IN (:categories)')
            ->setParameter('categories', $business->getOwner()->getAbleVendorCategories())
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $this->render('@LocalsBestUser/vendor/directory.html.twig', [
            'categories' => $categories,
            'vendors' => $vendors,
        ]);
    }
    
    public function sliderNewAction(Request $request, $businessType)
    {
        
        $biz = $this->getUser()->getBusinesses()[0];
        $u = $biz->getOwner();
        $vendors_array=[];
        $vendorsId_array=[];
        $vendors = $this->getUser()->getVendorsByCategory2($u, $businessType);

        if ($request->request->has('vendor_id')) {
            foreach ($vendors as $vendor) {
                if($vendor->getId() != $request->request->get('vendor_id')) {
                    $vendors->removeElement($vendor);
                }
            }
        }

        if (count($vendors) == 0) {
            // Return JSON response
            return new JsonResponse(array(
                'message' => 'No matching vendors found'
            ), 404);
        }
        
       foreach($vendors as $vendor){
           foreach($vendor->getBusinesses() as $vendorBusiness){
               array_push($vendors_array,$vendorBusiness->getName());
               array_push($vendorsId_array,$vendorBusiness->getId());
           }
       }
        
        return new JsonResponse(array('vendors' => $vendors_array, 'vendorsId_array' => $vendorsId_array));
    }
}
