<?php

namespace LocalsBest\UserBundle\Controller;

use DateTime;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\CommonBundle\Entity\Tag;
use LocalsBest\UserBundle\Dbal\Types\InviteStatusType;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\Address;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\Contact;
use LocalsBest\UserBundle\Entity\DocumentUser;
use LocalsBest\UserBundle\Entity\Email;
use LocalsBest\UserBundle\Entity\Phone;
use LocalsBest\UserBundle\Entity\Property;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\AddUserType;
use LocalsBest\UserBundle\Form\AsSignSettingsType;
use LocalsBest\UserBundle\Form\ChampionSettingsType;
use LocalsBest\UserBundle\Form\DocumentUserType;
use LocalsBest\UserBundle\Form\InviteType;
use LocalsBest\UserBundle\Form\TagType;
use LocalsBest\UserBundle\Form\VendorEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class UserController extends Controller
{

    /**
     * Users Summary page
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function indexAction(Request $request)
    {
        // Check user password for default values
        if (!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        if (
            !$this->get('localsbest.checker')->forAddon('company directory', $this->getUser())
            && !$this->get('localsbest.checker')->forAddon('transactions', $this->getUser())
        ) {
            $this->addFlash(
                'danger',
                'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket'
            );
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        // get Status filter
        $status = $request->get('status', 'active');

        $params = $request->query->all();

        if(!isset($params['status'])) {
            $params['status'] = $status;
        }

        $business = $this->getBusiness();

        if ($business === null) {
            $this->addFlash(
                'danger',
                'Sorry, there problem with information about your business. Please, try to logout and login again.'
            );
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        if (!is_null($business) && $business->getId() == 179) {
            return $this->render(
                '@LocalsBestUser/user/shredding-index.html.twig',
                [
                    'status' => $status == 'active' ? 'archived' : 'active',
                    'params' => http_build_query($params),
                    'paramsArray' => $params
                ]
            );
        } else {
            return $this->render(
                '@LocalsBestUser/user/index.html.twig',
                [
                    'status' => $status == 'active' ? 'archived' : 'active',
                    'params' => http_build_query($params),
                    'paramsArray' => $params
                ]
            );
        }
    }

    /**
     * Create User
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function addAction(Request $request)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }
        // check user permisions
        $this->restrictTo('ASSIST_MANAGER');

        $user = new User();
        
        $error = '';
        $business = null;

        // Get Current Business
        if($this->get('session')->get('current_business')) {
            $business = $this->getRepository('LocalsBestUserBundle:Business')
                ->find($this->get('session')->get('current_business'));
        }
        
        $user->getContact()->addEmail(new Email());
        $user->getContact()->addPhone(new Phone());

        $clientRoleAble = $this->get('localsbest.checker')->forAddon('cd client invitation', $this->getUser());
        $teamLeadRoleAble = $this->get('localsbest.checker')->forAddon('team', $this->getUser());

        // Create User Form object
        $form = $this->createForm(AddUserType::class, $user, [
            'user' => $this->getUser(),
            'clientRoleAble' => $clientRoleAble,
            'teamLeadRoleAble' => $teamLeadRoleAble,
        ]);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $form->getData();
                /** @var Email $email */
                foreach ($user->getContact()->getEmails() as $email) {
                    $insertEmail = $email->getEmail();
                }
                foreach ($user->getContact()->getPhones() as $phone) {
                    $insertPhone = $phone->getNumber();
                }
                $em = $this->getDoctrine()->getManager();
                // Disable Soft Delete Filter
                $em->getFilters()->disable('softdeleteable');
                // check email for existing in DB
                $emailExists = $em->getRepository('LocalsBestUserBundle:Email')->findOneByEmail($insertEmail);
                // check username for existing in DB
                $usernameExist = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $user->getUsername()]);

                if ($insertEmail === null) {
                    $form->get('contact')->get('emails')->addError(new FormError('Email address can not be empty.'));
                    $error = 'Email address can not be empty.';
                } elseif ($insertPhone == '') {
                    $form->get('contact')->get('phones')->addError(new FormError('Phone Number can not be empty.'));
                    $error = 'Phone Number can not be empty.';
                } elseif ($emailExists) {
                    $form->get('contact')->get('emails')
                        ->addError(new FormError('This email already exists. Please choose a different one'));
                    $error = 'Email entered is already registered. Please try a new email';
                } elseif($usernameExist) {
                    $error = 'Username entered is already registered. Please try a new username';
                    $form->get('username')->addError(new FormError('This username already exists. Please choose a different one'));

                } else {
                    // Create new User
                    $user->setCreatedBy($this->getUser());
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($password);
                    $createdUser = $this->getUser();
                    
                    $role = [
                        'ROLE_ADMIN',
                        'ROLE_CUSTOMER_SERVIC',
                        'ROLE_NETWORK_MANAGER',
                        'ROLE_MANAGER',
                        'ROLE_TECH_DIRECTOR'
                    ];

                    // Attach user to business
                    if(
                        $business != null
                        && !in_array($user->getRole()->getRole(), $role)
                        && $createdUser->getRole()->getRole() !== 'ROLE_ADMIN'
                    ) {
                        $user   ->addBusiness($business);
                        $business->addStaff($user);
                        $em->getRepository('LocalsBest\UserBundle\Entity\Business')->save($business);
                    }
                    // Attache contact to user
                    if(!in_array($this->getUser()->getRole()->getRole(), array('ROLE_ADMIN','ROLE_CUSTOMER_SERVIC'))) {
                        $contact = new AllContact();
                        $contact
                            ->setFirstName($createdUser->getFirstName())
                            ->setLastName($createdUser->getLastName())
                            ->setNumber($createdUser->getPrimaryPhone()->getNumber())
                            ->setEmail($createdUser->getPrimaryEmail()->getEmail())
                            ->setUser($createdUser)
                            ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                            ->setCreatedBy($user)
                        ;

                        $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);
                        $user->addAllContact($contact);
                    }

                    if ($user->getPrimaryPhone() === null) {
                        $user->setPrimaryPhone($user->getContact()->getPhones()->first());
                    }

                    if ($user->getPrimaryEmail() === null) {
                        $user->setPrimaryEmail($user->getContact()->getEmails()->first());
                    }

                    $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                    // Send Email
                    $this->getMailMan()->sendWelcomeMail($user);
                    // Show flash message
                    $this->addSuccessMessage('User created successfully!');

                    return $this->redirect($this->generateUrl('users_profile_edit', array('username' => $user->getUsername())));
                }
            }
        }
        // Return params to view
        return array(
            'form' => $form->createView(),
            'error' => $error,
            'user' => $user
        );
    }

    /**
     * Invite User
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function inviteAction(Request $request)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }
        // Get role level
        $level  = $this->getUser()->getRole()->getLevel();
        
        if (!$level) {
            throw $this->createNotFoundException('Access Denied');   
        }

        $em = $this->getDoctrine()->getManager();

        $error = '';
        $business = null;

        // Get current Business
        if($this->get('session')->get('current_business')) {
            $business = $this->getRepository('LocalsBestUserBundle:Business')
                ->find($this->get('session')->get('current_business'));
        }
        // get business staff
        $users = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findStaffs($this->getUser(), $business);

        // get invite by user
        $inviteUsers = $this->getRepository('LocalsBest\UserBundle\Entity\Invite')
            ->findMyObjects($this->getUser(), $users);

        $clientRoleAble = $this->get('localsbest.checker')->forAddon('cd client invitation', $this->getUser());
        $teamLeadRoleAble = $this->get('localsbest.checker')->forAddon('team', $this->getUser());

        // create Invite Form object
        $form = $this->createForm(InviteType::class, null, [
            'user' => $this->getUser(),
            'clientRoleAble' => $clientRoleAble,
            'teamRoleAble' => $teamLeadRoleAble,
        ]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // Get Invite data
                $invite = $form->getData();
                $inviteLevel = $invite->getRole()->getLevel();
                $inviteEmail = $invite->getEmail();

                $emailExists = $em->getRepository('LocalsBestUserBundle:Email')->findOneByEmail($inviteEmail);

                if($level > $inviteLevel) {
                    $error = 'Invalid Role';
                    $form->get('role')->addError(new FormError('Invalid Role'));
                } else {
                    if ($invite->getRole()->getId() == 8) {
                        $time   = time();
                        $randNum = json_encode([
                            'category' => $form->get('category')->getData(),
                            'businessType' => $form->get('businessType')->getData()
                        ]);
                        $token = base64_encode($time . ':' . $randNum);
                    } else {
                        $time   = time();
                        $randNum = rand();
                        $token = base64_encode($time . ':' . $randNum);
                    }

                    // Set info to Invite
                    $invite
                        ->setToken($token)
                        ->setCreatedBy($this->getUser())
                        ->setStatus(InviteStatusType::INVITE)
                        ->setBusiness($business);
                    // Save changes
                    $this->getDoctrine()->getManager()
                        ->getRepository('LocalsBest\UserBundle\Entity\Invite')->save($invite);
                    // Send Email
                    $this->getMailMan()->sendInvitationMail($invite);
                    // Show flash message
                    $this->addSuccessMessage('User invited successfully');
                    // redirect User
                    return $this->redirectToRoute('users_invite');
                }
            }
        }
        // Return params to view
        return array(
            'form' => $form->createView(),
            'error' => $error,
            'inviteUsers' => $inviteUsers
        );
    }

    /**
     * User View Page
     *
     * @param $username
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function viewAction($username)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        if (
            !$this->get('localsbest.checker')->forAddon('customers', $this->getUser())
            && !$this->get('localsbest.checker')->forAddon('employee profiles', $this->getUser())
            && !in_array($this->getUser()->getId(), [2338])
        ) {
            $this->addFlash(
                'danger',
                'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket'
            );
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $objectType = 'LocalsBestUserBundle:User';
        // Get user by username
        $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(array('username' => $username));

        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found with username - %s', $username));
        }
        // Get business owner
        $u = $this->getBusiness()->getOwner();
        // Get business staff
        $staff = $this->getStaffs($u);

        // Check user permissions
        if(
            $user != $this->getUser()
            && !in_array($user, $staff)
            && $this->getUser()->getRole()->getLevel() != 5
            && ($user !== $this->getBusiness()->getOwner())
        ) {
            throw new AccessDeniedHttpException('You don\'t have permissions to access this user.');
        }
        
        $objectId = $user;
        $object = 'user';
        // Get user events
        $userEvents = $em->getRepository('LocalsBest\UserBundle\Entity\Event')
            ->findMyEvents($this->getUser(),$objectId,$object);
        // Get user documents
        $userDocuments = $em->getRepository('LocalsBest\UserBundle\Entity\DocumentUser')
            ->findMyDocuments($this->getUser(), $objectId, $object);
        // Get staff
        $myStaffs = $this->getStaffs();
        // Get user Notes
        $object = 'LocalsBestUserBundle:User';
        $objectId = $user;
        $notes = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
            ->findMyNotes($this->getUser(),$object, $objectId, $myStaffs);
        // Get Tags
        $tagsArray = array();
        $tags = $em->getRepository('LocalsBest\CommonBundle\Entity\Tag')
            ->findMyTags($this->getUser(),$objectId,$object);

        /** @var Tag $tagObject */
        foreach ($tags as $tagObject) {
            $tagsArray[] = $tagObject->getTag();
        }
        // create Tags Form objects
        $tagForm = $this->createForm(TagType::class);
        // set tags to Tags Form
        $tagForm->get('tag')->setData(implode(', ', $tagsArray));
        //  Return params to view
        return [
            'user' => $user,
            'notes' => $notes,
            'form' => $tagForm->createView(),
            'objectType' => $objectType,
            'tagsArray' => $tagsArray,
            'userDocuments' => $userDocuments,
            'userEvents' => $userEvents
        ];
    }

    /**
     * Display user information
     *
     * @param $username
     *
     * @return Response
     */
    public function viewContentAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $objectType = 'LocalsBestUserBundle:User';
        // Get user by username
        $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found with username - %s', $username));
        }
        // Get business owner
        $u = $this->getBusiness()->getOwner();
        $staff = $this->getStaffs($u);
        // Check user permissions
        if(
            $user != $this->getUser()
            && !in_array($user, $staff)
            && $this->getUser()->getRole()->getLevel() != 5
            && ($user !== $this->getBusiness()->getOwner())
        ) {
            throw new AccessDeniedHttpException('You don\'t have permissions to access this user.');
        }

        $objectId = $user;
        $object = 'user';
        // Get User Events
        $userEvents = $em->getRepository('LocalsBest\UserBundle\Entity\Event')
            ->findMyEvents($this->getUser(),$objectId,$object);
        // Get User Documents
        $userDocuments = $em->getRepository('LocalsBest\UserBundle\Entity\DocumentUser')
            ->findMyDocuments($this->getUser(), $objectId, $object);
        // get business staff
        $myStaffs = $this->getStaffs();
        // Get User Notes
        $object = 'LocalsBestUserBundle:User';
        $objectId = $user;
        $notes = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
            ->findMyNotes($this->getUser(),$object, $objectId, $myStaffs);
        // Get User Tags
        $tagsArray = array();
        $tags = $em->getRepository('LocalsBest\CommonBundle\Entity\Tag')->findMyTags($this->getUser(),$objectId,$object);

        /** @var Tag $tagObject */
        foreach ($tags as $tagObject) {
            $tagsArray[] = $tagObject->getTag();
        }
        // Create Tag Form object
        $tagForm = $this->createForm(TagType::class);
        $tagForm->get('tag')->setData(implode(', ', $tagsArray));
        // Render view with params
        return $this->render(
            '@LocalsBestUser/user/_view-content.html.twig',
            [
                'user' => $user,
                'notes' => $notes,
                'form' => $tagForm->createView(),
                'objectType' => $objectType,
                'tagsArray' => $tagsArray,
                'userDocuments' => $userDocuments,
                'userEvents' => $userEvents
            ]
        );
    }

    /**
     * Display User details
     *
     * @param $username
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function detailAction($username)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        $em = $this->getDoctrine()->getManager();
        // Get User by username
        $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found with username - %s', $username));
        }
        // Return params to view
        return [
            'user' => $user
        ];
    }

    /**
     * Check user by primary email or phone
     *
     * @param $type
     * @param string $checkType
     * @param string $element
     *
     * @return array|JsonResponse
     * @Template
     */
    public function ajaxCheckedAction($type, $checkType, $element)
    {
        // Select entity type
        if($checkType === 'primaryEmail') {
            $object = 'LocalsBestUserBundle:Email';
            $objectType = 'findOneByEmail';
        } else {
            $object = 'LocalsBestUserBundle:Phone';
            $objectType = 'findByNumber';
            $element = str_replace(['(', ')', '-', ' '], '', $element);
        }

        $em = $this->getDoctrine()->getManager();

        $users = null;
        // Get Entity object
        $objectExists = $em->getRepository($object)->$objectType($element);

        if($objectExists) {
            // Get User by Entity object
            $users = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findBy([$checkType => $objectExists]);
        }

        if (empty($users)) {
            // Return fail JSON response
            return new JsonResponse(['message' => 'No matching users found'], 404);
        }
        // Return params to view
        return array(
            'users'  => $users,
            'type'  => $type
        );
    }

    /**
     * Display list of users and contacts
     *
     * @param $status
     *
     * @return array
     * @Template
     */
    public function loadAjaxAction($status)
    {
        $totalMembers = array();
        $em = $this->getDoctrine()->getManager();
        
        if($status === 'User') {
            // Disable Soft Delete filter
            $em->getFilters()->disable('softdeleteable');
            // Get business staff
            if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
                $users = array();
                $businesses = $this->getUser()->getBusinesses();

                foreach ($businesses as $business) {
                    $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                        ->findOneBy(['id' => $business->getOwner()->getid()]);
                    $users[] = $user;
                }
            } else {
                $business = $this->getBusiness();
                $users = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                    ->findStaffs($this->getUser(), $business);
            }
            if ($users) {
                foreach ($users as $user) {
                    $totalMembers[] = $user;
                }
            }
        } elseif( $this->getUser()->getRole()->getLevel() == 6 && $status == 'Team' ) {
            $user = $this->getUser();
            // Get Team Entity by leader
            $team = $em->getRepository('LocalsBestUserBundle:Team')->findOneBy(['leader' => $user]);

            if(!is_null($team)) {
                // Get team members
                $users = $team->getAgents();
            }

            if(isset($users) && $users) {
                foreach ($users as $user) {
                    $totalMembers[] = $user;
                }
            }
        } elseif ($status === 'Company') {
            // Disable Soft Delete filter
            $em->getFilters()->disable('softdeleteable');
            // Get Business staff
            if($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
                $users = array();
                $businesses = $this->getUser()->getBusinesses();

                foreach($businesses as $business) {
                    $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                        ->findOneBy(['id' => $business->getOwner()->getid()]);
                    $users[] = $user;
                }
            } else {
                $users = $em->getRepository('LocalsBestUserBundle:User')
                    ->findFullStaffs($this->getUser(), $this->getBusiness());
            }

            if($users) {
                foreach ($users as $user) {
                    $totalMembers[] = $user;
                }
            }
        } elseif ($status === 'Contact') {
            // Get business staff
            if($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
                $users = array();
                $businesses = $this->getUser()->getBusinesses();

               foreach($businesses as $business) {
                   $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                       ->findOneBy(array('id' => $business->getOwner()->getid()));
                   $users[] = $user;
               }
            } else {
                $users = $this->getStaffs();
            }
            // Get users Contacts
            $contacts = $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')
                ->findMyObjects($this->getUser(), $users);
            if($contacts) {
                foreach ($contacts as $contact) {
                    $totalMembers[] = $contact;
                }
            }
        } elseif ($status === 'All') {
            // Disable Soft Delete filter
            $em->getFilters()->disable('softdeleteable');
            // Get business staff
            if($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
                $users = array();
                $businesses = $this->getUser()->getBusinesses();
               foreach($businesses as $business) {
                   $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                       ->findOneBy(array('id' => $business->getOwner()->getid()));
                   $users[] = $user;
               }
            } else {
                $business = $this->getBusiness();
                $users = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                    ->findStaffs($this->getUser(), $business);
            }
            if($users) {
                foreach ($users as $user) {
                    $totalMembers[] = $user;
                }
            }
            // Enable Soft Delete filter
            $em->getFilters()->enable('softdeleteable');
            // Get users Contacts
            $contacts = $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')
                ->findMyObjects($this->getUser(), $users);
            if($contacts) {
                foreach ($contacts as $contact) {
                    $totalMembers[] = $contact;
                }
            }
        }
        // Return params to view
        return [
            'members' => $totalMembers
        ];
    }

    /**
     * Return owner of object
     *
     * @param string $type
     * @param int $id
     *
     * @return Response
     * @Template
     */
    public function checkAjaxAction($type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $response = [];
        // Get object entity
        $file = $this->findOr404($type, array('id' => $id), 'Invalid item');
        if($type === ObjectTypeType::Job)
        {

        } elseif($type === ObjectTypeType::Document) {
            // Get User who create object entity
            $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')->find($file->getCreatedBy());
            // prepare response array
            $response = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            );
        }elseif($type === ObjectTypeType::Transaction) {

            
        }elseif ($type === ObjectTypeType::Contact) {

            
        }elseif ($type === ObjectTypeType::User) {

        }
        // Return JSON response
        return new Response(json_encode($response));
    }

    /**
     * Delete User
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        $em = $this->getDoctrine()->getManager();
        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');
        // Get User by ID
        $user = $this->findOr404('LocalsBestUserBundle:User', ['id' => $id]);

        if (!$user) {
            throw $this->createNotFoundException('No such user found');
        }
        // Check user permissions
        if ($user->getRole()->getLevel() <= $this->getUser()->getRole()->getLevel()) {
            throw new AccessDeniedException();
        }

        if(!is_null($user->getDeleted())) {
            // Un-delete User
            $user->setDeleted(null);
        } else {
            // Delete User
            $em->remove($user);
        }
        // Update DB
        $em->flush();

        /// Return User to Prev Page
        $referrer = $request->headers->get('referer');
        return new RedirectResponse($referrer);
    }

    /**
     * Make User Doc Approver
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function makeDocumentApproverAction($id)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }
        // Check user permissions
        $this->restrictTo('manager');

        // Get User by ID
        /** @var User $user */
        $user = $this->findOr404('LocalsBestUserBundle:User', ['id' => $id]);
        // Check users permissions
        if ($user->getRole()->getLevel() <= $this->getUser()->getRole()->getLevel()) {
            throw new AccessDeniedException();
        }
        // Make User Doc Approver
        $user->makeDocumentApprover();
        // Update changes
        $this->getDoctrine()->getRepository('LocalsBestUserBundle:User')->save($user);
        // Show flash message
        $this->addFlash('success', $user->getFirstName() . ' ' . $user->getLastName() . ' can Approve Documents.');
        // Redirect user
        return $this->redirectToRoute('users_index');
    }

    /**
     * Removes a User's Document Approver permission
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function unMakeDocumentApproverAction($id)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }
        // Check user permission
        $this->restrictTo('manager');
        // Get User by ID
        /** @var User */
        $user = $this->findOr404('LocalsBestUserBundle:User', array('id' => $id));
        // Get users permissions
        if ($user->getRole()->getLevel() <= $this->getUser()->getRole()->getLevel()) {
            throw new AccessDeniedException();
        }
        
        if ($user->isDocumentApprover()) {
            // Update User Info
            $user->makeDocumentApprover(false);
            $this->getDoctrine()->getRepository('LocalsBestUserBundle:User')->save($user);
        }
        // Show flash message
        $this->addFlash('warning', $user->getFirstName() . ' ' . $user->getLastName() . ' can not Approve Documents.');
        // Redirect user
        return $this->redirect($this->generateUrl('users_index'));
    }

    /**
     * Edit User-vendor info
     *
     * @param Request $request
     * @param $vendorId
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function vendorEditAction(Request $request, $vendorId)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        $em = $this->getDoctrine()->getManager();
        // get User-vendor by ID
        $vendor = $em->getRepository('LocalsBest\UserBundle\Entity\User')->find($vendorId);
        // CreateVendor Edit Form object
        $form = $this->createForm(VendorEditType::class, $vendor);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                // Save changes
                $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($data);
                // Show flash message
                $this->addSuccessMessage('Service category level successfully updated.');
                // Redirect user
                return $this->redirectToRoute('service_index');
            }
        }
        // Return params to view
        return array(
            'vendor'  => $vendor->getFirstname(),
            'form'  => $form->createView(),
        );
    }

    /**
     * Attach Document to User
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function documentAddAction(Request $request, $id)
    {
        // Get User by ID
        $userTarget = $this->findOr404('LocalsBestUserBundle:User', ['id' => $id], 'Invalid item');
        // Create User Document
        $document = new DocumentUser();
        $document->setStatus($this->getRepository('LocalsBestUserBundle:Document')->getDefaultStatus());
        $user = $this->getUser();
        // Create User Document Form object
        $documentForm = $this->createForm(DocumentUserType::class, $document);
        $documentForm->handleRequest($request);
        if($request->getMethod() == "POST") {
            if ($documentForm->isValid()) {
                $document = $documentForm->getData();
                // Get current business
                $business = $this->getRepository('LocalsBestUserBundle:Business')
                    ->find($this->get('session')->get('current_business'));
                // Update document info
                $document
                    ->setOwner($business)
                    ->setCreatedBy($user)
                    ->setUser($userTarget);
                // Attache document to user
                $userTarget->addDocument($document);
                // Save changes
                $this->getRepository('LocalsBestUserBundle:DocumentUser')->save($document);
                $this->getRepository('LocalsBestUserBundle:User')->save($userTarget);
                // Redirect User to prev page
                return $this->redirect($request->server->get('HTTP_REFERER'));
            } else {
                // Show flash message
                $this->addFlash('danger', 'You try to upload wrong File Type.');
                // Redirect user to prev page
                return $this->redirect($request->server->get('HTTP_REFERER'));
            }
        }
        // Return params to view
        return [
            'documentForm' => $documentForm->createView(),
            'id' => $id
        ];
    }

    /**
     * Detach document from user
     *
     * @param Request $request
     * @param $slug
     *
     * @return RedirectResponse
     */
    public function documentRemoveAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Document by slug
        $documentUser = $em->getRepository('LocalsBestUserBundle:DocumentUser')->findOneBy(['slug' => $slug]);
        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');
        // Delete Document
        $em->remove($documentUser);
        // update DB
        $em->flush();
        // Redirect user to prev page
        $referrer = $request->headers->get('referer');
        return new RedirectResponse($referrer);
    }

    /**
     * Import Users from XML file to system
     * Don't used
     */
    public function excelImportAction()
    {
        die('imported');
        set_time_limit(0);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('For Mike.xlsx');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $em = $this->getDoctrine()->getManager();

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . 'L' . $row, NULL, TRUE, FALSE);

            if ($rowData[0][1] == '') {
                break;
            }

            //  Insert row data array into your database of choice here
            $user = new User();
            $business = $em->getRepository('LocalsBest\UserBundle\Entity\Business')->find(2);
            $role = $em->getRepository('LocalsBest\UserBundle\Entity\Role')->find(7);
            $manager = $business->getOwner();

            $email = new Email();
            $email->setEmail($rowData[0][3]);

            $phone = new Phone();
            $phone->setNumber($rowData[0][5]);
            $phone->setType('O');

            $property = new Property();
            $address = new Address();

            if ($rowData[0][6] !== '' && !is_null($rowData[0][6])) {
                $addressArray = explode(',', $rowData[0][6]);

                $address->setStreet($addressArray[0]);
                $address->setCity($addressArray[1]);
                $address->setState($addressArray[2]);
                $address->setZip($addressArray[3]);
                $this->getDoctrine()->getManager()->persist($address);

                $property->setAddress($address);
                $property->setType('Single_Family_Home');
                $property->setUser($user);
                $this->getDoctrine()->getManager()->persist($property);
            }

            if( $rowData[0][7] !== '' && $rowData[0][7] !== 'Cannot Find' ) {
                $boards = explode('/', $rowData[0][7]);

                foreach ($boards as $board) {
                    $board = trim($board);
                    $boardObj = $em->getRepository('LocalsBest\UserBundle\Entity\Association')
                        ->findOneBy(['title' => $board]);
                    if($boardObj) {
                        $user->addAssociation($boardObj);
                    }
                }
            }

            $contact = new Contact();
            $user->setContact($contact);

            $user->getContact()->addEmail($email);
            $email->setContact($contact);
            $user->getContact()->addPhone($phone);
            $phone->setContact($contact);

            $em->persist($contact);

            $em->persist($phone);
            $em->persist($email);

            $user->setPrimaryPhone($phone);
            $user->setPrimaryEmail($email);

            $user->setRole($role);

            $usernameExist = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                ->findOneBy(['username' => $rowData[0][3]]);

            if ($usernameExist) {
                $user->setUsername('agent-' . $rowData[0][3]);
                echo 'agent-' . $rowData[0][3] . '<br>';
            } else {
                $user->setUsername($rowData[0][3]);
            }

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $user->setPassword($encoder->encodePassword('admin', $user->getSalt()));
            $user->addBusiness($business);
            $business->addStaff($user);
            $user->setCreatedBy($manager);

            $user->setFirstName($rowData[0][0]);
            $user->setLastName($rowData[0][1]);

            if ($rowData[0][4] !== 'No Picture' || $rowData[0][4] !== 'n/a') {
                $user->setWebsite($rowData[0][4]);
            }

            if ($rowData[0][4] !== 'No Picture' && $rowData[0][4] !== 'n/a' && $rowData[0][4] !== '') {
                $user->setWebsite($rowData[0][4]);
            }

            if ($rowData[0][10] !== 'N/A') {
                $user->setStateLicenseId($rowData[0][10]);
            }

            $contact = new AllContact();
            $contact
                ->setFirstName($manager->getFirstName())
                ->setLastName($manager->getLastName())
                ->setNumber($manager->getPrimaryPhone()->getNumber())
                ->setEmail($manager->getPrimaryEmail()->getEmail())
                ->setUser($manager)
                ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                ->setCreatedBy($user)
            ;

            $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);
            $user->addAllContact($contact);

            if (
                $sheet->getCell('I' . $row)->getFormattedValue() != ''
                && $sheet->getCell('I' . $row)->getFormattedValue() != '?'
            ) {
                $birthday = explode('/', $sheet->getCell('I' . $row)->getFormattedValue());
                $user->setBirthday(new DateTime(
                    date("Y-m-d H:i:s", strtotime($birthday[2] . '-' . $birthday[1] . '-' . $birthday[0]))
                ));
            }

            $joinCompany = explode('/', $sheet->getCell('J' . $row)->getFormattedValue());
            $user->setJoinedCompanyDate(new DateTime(
                date("Y-m-d H:i:s", strtotime($joinCompany[2] . '-' . $joinCompany[1] . '-' . $joinCompany[0]))
            ));

            if ($sheet->getCell('L' . $row)->getFormattedValue() != 'N/A') {
                $licenseExpiration = explode('/', $sheet->getCell('L' . $row)->getFormattedValue());
                $user->setLicenseExpirationDate(new DateTime(
                    date(
                        "Y-m-d H:i:s",
                        strtotime($licenseExpiration[2] . '-' . $licenseExpiration[1] . '-' . $licenseExpiration[0])
                    )
                ));
            }

            $em->persist($business);
            $em->persist($user);
            $em->flush();
        }

        die('done');
    }

    /**
     * Increase User Role Level
     *
     * @param Request $request
     * @param null $agentId
     *
     * @return RedirectResponse
     */
    public function increaseLevelAction(Request $request, $agentId = null)
    {
        if($this->getUser()->getRole()->getLevel() !== 4) {
            // Show flash message
            $this->addFlash('danger', 'Access Denied!');
            // Redirect user to prev page
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
        if(is_null($agentId)){
            // Show flash message
            $this->addFlash('danger', 'Empty user ID!');
            // Redirect user to prev page
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $em = $this->getDoctrine()->getManager();
        // Get User by ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($agentId);

        if(is_null($user)){
            // Show flash message
            $this->addFlash('danger', 'Wrong user ID!');
            // Redirect user to prev page
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
        // Get User current role level
        $userRole = $user->getRole()->getLevel();
        if($userRole > 5 && $userRole <=7) {
            // Update role level
            $userRole--;

            $checker = $this->get('localsbest.checker');

            if ($userRole == 6 && !$checker->forAddon('team', $this->getUser())) {
                $userRole--;
            }

            $user->setRole($em->getRepository('LocalsBestUserBundle:Role')->find($userRole));
        }
        // Update DB
        $em->flush();
        // Show flash message
        $this->addFlash('success', 'User Role updated successfully.');
        // Redirect user to prev page
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * Decrease User Role Level
     *
     * @param Request $request
     * @param null $agentId
     *
     * @return RedirectResponse
     */
    public function decreaseLevelAction(Request $request, $agentId = null)
    {
        if($this->getUser()->getRole()->getLevel() !== 4) {
            // Show flash message
            $this->addFlash('danger', 'Access Denied!');
            // Redirect user to prev page
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
        if(is_null($agentId)){
            // Show flash message
            $this->addFlash('danger', 'Empty user ID!');
            // Redirect user to prev page
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $em = $this->getDoctrine()->getManager();
        // Get User by ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($agentId);

        if(is_null($user)){
            // Show flash message
            $this->addFlash('danger', 'Wrong user ID!');
            // Redirect user to prev page
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
        // Get user current role level
        $userRole = $user->getRole()->getLevel();
        if($userRole >= 5 && $userRole <7) {
            // Update role level
            $userRole++;

            $checker = $this->get('localsbest.checker');

            if ($userRole == 6 && !$checker->forAddon('team', $this->getUser())) {
                $userRole++;
            }

            $user->setRole($em->getRepository('LocalsBestUserBundle:Role')->find($userRole));
        }
        // update DB
        $em->flush();
        // Show flash message
        $this->addFlash('success', 'User Role updated successfully.');
        // Redirect user to prev page
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * Gt User by name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getUserByNameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Get request string
        $nameStr = $request->query->get('name');
        // Split request string
        $name = explode(' ', $nameStr);
        // Get User by first and last names
        $user = $em->getRepository('LocalsBestUserBundle:User')
            ->findOneBy(['firstName' => $name[0], 'lastName' => last($name)]);
        // Return JSON response
        if($user !== null) {
            return new JsonResponse([
                'result' => 1,
                'id' => $user->getId(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName()
            ]);
        } else {
            return new JsonResponse(['result' => 0]);
        }

    }

    /**
     * Change User type
     *
     * @param $userId
     *
     * @return JsonResponse
     */
    public function toggleUserTypeAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        // Get User by ID
        /** @var User $user */
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($userId);
        // Change User Type
        if( $user->getUserTypeId() == $user::TYPE_REGULAR ) {
            $user->setUserType($user::TYPE_SPECIAL);
        } elseif( $user->getUserTypeId() == $user::TYPE_SPECIAL) {
            $user->setUserType($user::TYPE_REGULAR);
        }
        // Update DB
        $em->flush();
        $type = $user->getUserType();
        $html = '<a data-userid="' . $user->getId() . '"  href="#" class="change-user-type btn btn-sm btn-'
            . $type['color'] . '">' . $type['short_name'] . '</a>';
        // Return JSON response
        return new JsonResponse([
            'html' => $html
        ]);
    }

    /**
     * Show form for search users for masking
     *
     * @return Response
     */
    public function maskingAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('LocalsBestUserBundle:Business')->createQueryBuilder('b');

        $qb
            ->select('b.id, b.name')
            ->where('b.deleted is null')
            ->orderBy('b.name')
        ;

        $sec = $this->get('security.token_storage');

        if ($this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            foreach($sec->getToken()->getRoles() as $role) {
                if ($role instanceof SwitchUserRole) {
                    $prevUser = $role->getSource()->getUser();
                    $realUser = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $prevUser->getUsername()]);
                }
            }
        }

        $availableBusiness = false;

        if (
            $this->isGranted('ROLE_CUSTOMER_SERVIC')
            || (
                isset($realUser)
                && $realUser->getRole()->getLevel() <= 2
            )
        ) {
            $availableBusiness = true;
        }

        $businesses = $qb->getQuery()->getArrayResult();

        return $this->render('@LocalsBestUser/user/masking.html.twig', [
            'businesses' => $businesses,
            'businessesAvailable' => $availableBusiness,
        ]);
    }

    /**
     * Search users by user query for masking
     *
     * @param Request $request
     *
     * @return Response
     */
    public function usersForMaskingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $request->query->get('query', null);

        $sec = $this->get('security.token_storage');

        if ($this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            foreach($sec->getToken()->getRoles() as $role) {
                if ($role instanceof SwitchUserRole) {
                    $prevUser = $role->getSource()->getUser();
                    $realUser = $em->getRepository('LocalsBestUserBundle:User')
                        ->findOneBy(['username' => $prevUser->getUsername()]);
                }
            }
        }

        $isAdmin = false;

        if (
            $this->isGranted('ROLE_CUSTOMER_SERVIC')
            || (
                isset($realUser)
                && $realUser->getRole()->getLevel() <= 2
            )
        ) {
            $qBusiness = $request->query->get('business', null);
            $isAdmin = true;
        } else {
            $qBusiness = $this->getBusiness()->getId();
        }

        $users = [];

        if ($q !== null && $q != '') {
            $qb = $em
                ->getRepository('LocalsBestUserBundle:User')
                ->createQueryBuilder('u');

            $qb
                ->join('u.contact', 'c')
                ->join('c.emails', 'e')
                ->where(
                    $qb->expr()->orX(
                        $qb->expr()->like("CONCAT(u.firstName, CONCAT(' ', u.lastName))", ':query'),
                        $qb->expr()->like('u.firstName', ':query'),
                        $qb->expr()->like('u.lastName', ':query'),
                        $qb->expr()->like('u.username', ':query'),
                        $qb->expr()->like('e.email', ':query')
                    )
                )
            ;

            if (!$isAdmin) {
                $qb
                    ->join('u.role', 'r')
                    ->andWhere('r.level < 8')
                    ->andWhere('r.level > 3')
                ;
            }

            if ($qBusiness !== null && $qBusiness != '') {
                $qb
                    ->join('u.businesses', 'b')
                    ->andWhere('b.id = :business')
                    ->setParameter('business', $qBusiness)
                ;
            }

            $qb
                ->setParameter('query', '%' . $q . '%')
                ->orderBy('u.firstName', 'ASC')
                ->addOrderBy('u.lastName', 'ASC')
            ;

            $users = $qb->getQuery()->getResult();
        }

        return $this->render(
            '@LocalsBestUser/user/masking-users-ajax.html.twig',
            ['users' => $users]
        );
    }

    public function AsSignInventoryAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('LocalsBestUserBundle:User')->find($userId);

        $form = $this->createForm(AsSignSettingsType::class, $user, [
            'action' => $this->generateUrl('as_sign_update_inventory', ['userId' => $userId]),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('members_details', ['id' => $userId]);
            } else {
                $this->addFlash('danger', 'Wrong value for Inventory.');
                return $this->redirectToRoute('members_details', ['id' => $userId]);
            }
        }

        return $this->render('@LocalsBestUser/user/as-sign-inventory.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    public function ChampionSizeOfTheBinAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('LocalsBestUserBundle:User')->find($userId);

        $form = $this->createForm(ChampionSettingsType::class, $user, [
            'action' => $this->generateUrl('champion_update_size_of_the_bin', ['userId' => $userId]),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('members_details', ['id' => $userId]);
            } else {
                $this->addFlash('danger', 'Wrong value for Size of the Bin.');
                return $this->redirectToRoute('members_details', ['id' => $userId]);
            }
        }

        return $this->render('@LocalsBestUser/user/champion-size-of-the-bin.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    public function userDocsSendEmailAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            // Get Documents IDs
            $documents = $request->query->get('documents');

            // Get Object Type
            $type = $request->query->get('type', 'users');

            switch ($type) {
                case 'jobs' :
                    $repository = 'LocalsBestUserBundle:DocumentJob';
                    break;
                case 'users' :
                default:
                    $repository = 'LocalsBestUserBundle:DocumentUser';
                    break;

            }

            // Get Documents Entities
            $docs = $em->getRepository($repository)->findBy(['id' => $documents]);

            // Render view with params
            return $this->render(
                '@LocalsBestUser/document/user-docs-send-email.html.twig',
                [
                    'docs' => $docs,
                    'requestType' => $type,
                ]
            );
        }
        die('403');
    }
}
