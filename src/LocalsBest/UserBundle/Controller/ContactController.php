<?php

namespace LocalsBest\UserBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\InviteStatusType;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\DocumentUser;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Invite;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\AllContactType;
use LocalsBest\UserBundle\Form\DocumentUserType;
use LocalsBest\UserBundle\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Description of ContactController
 *
 * @author pritam
 * 
 */
class ContactController extends SuperController
{
    public function indexAction(Request $request)
    {
        // Check User password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        // Check: does users business belong to special
        if (
            $this->getUser()->getBusinesses()->first()->getid() == 155
            || $this->getBusiness()->getId() == 23
            || $this->get('localsbest.checker')->forAddon('advanced contacts screen', $this->getUser())
        ) {
            // get filter value from request
            $filter = $request->query->get('filter', "all");
            // if filter empty set default value
            if (!in_array($filter, ['all', 'members', 'contacts'])) {
                $filter = "all";
            }

            // get status value from request
            $filterStatus = $request->query->get('filter_status', "all");

            // if status empty set default value
            if (!in_array($filterStatus, ['all', 'open', 'closed'])) {
                $filterStatus = "all";
            }

            $params = ['filter' => $filter, 'filterStatus' => $filterStatus];
            // render special view with params
            return $this->render('@LocalsBestUser/contact/special-index.html.twig', $params);
        } else {
            // get all contacts for current User
            $contacts = $this->getRepository('LocalsBest\UserBundle\Entity\AllContact')->findMyObjects($this->getUser());
            // render simple view with contacts
            return $this->render('@LocalsBestUser/contact/simple-index.html.twig', ['contacts' => $contacts]);
        }
    }

    /**
     * Display Create and Edit Contact Page
     *
     * @Template
     *
     * @param Request $request
     * @param int $id
     * @throws Exception
     * @return array|RedirectResponse
     */
    public function editAction(Request $request,$id = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($id !== null) {
            // Get Contact using ID
            /** @var AllContact $contact */
            $contact = $this->findOr404('LocalsBestUserBundle:AllContact', array('id' => $id), 'Contact not found');

            // Check Contact Status
            if ($contact->getStatus() === null) {
                $contact->setStatus($em->getReference('LocalsBestCommonBundle:Status', 1));
            }

            // Get All Contacts for current User
            /** @var ArrayCollection $contactList */
            $contactList = new ArrayCollection($em->getRepository('LocalsBest\UserBundle\Entity\AllContact')
                ->findAllContacts($this->getUser()));

            // Check does Contacts List contains current Contact
            if (!$contactList->contains($contact)) {
                throw  $this->createAccessDeniedException("You don't have permissions to access this contact.");
            }
            unset($contactList);
        }

        /** @var Business $business */
        $business = $this->getBusiness();

        // check action that we need: Create or Edit
        if ($id) {
            if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
                $myStaffs = array();
                $businesses = $this->getUser()->getBusinesses();

                // Get Business staff
               foreach ($businesses as $businessOption) {
                   $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                       ->findOneBy(['id' => $businessOption->getOwner()->getid()]);
                   $myStaffs[] = $user;
               }
            } else {
                // Get Business staff
                $myStaffs = $this->getStaffs();
            }
        } else {
            // create new Contact Entity
            $contact = new AllContact();
            // set some contact information
            $contact
                ->setStatus($em->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                ->setOwner($business)
                ->setCreatedBy($this->getUser())
            ;

            $myStaffs = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findAllBusiness($business->getOwner());
        }

        if ($this->get('localsbest.checker')->forAddon('client portals', $this->getUser())) {
            $allowInvite = true;
        } else {
            $allowInvite = false;
        }

        // Create Contact Form object
        $form = $this->createForm(AllContactType::class, $contact, [
            'user' => $this->getUser(),
            'staff' => $myStaffs,
            'allowInvite' => $allowInvite,
        ]);

        // if Contact Form was submit
        if ($request->getMethod() === 'POST') {
            // set request information to form
            $form->handleRequest($request);

            if ($form->isValid()) {
                // create Contact Entity using form information
                $contactData = $form->getData();

                // save new Contact
                $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contactData);

                // Create invitation if was checked
                if ($contactData->getInvitation() === true) {
                    $role = $em->getRepository('LocalsBestUserBundle:Role')->findOneBy(['level' => 8]);
                    $time   = time();
                    $randNum = rand();
                    $token = base64_encode($time . ':' . $randNum);
                    $invite = new Invite();
                    $invite->setToken($token)
                            ->setCreatedBy($this->getUser())
                            ->setEmail($contactData->getEmail())
                            ->setRole($role)
                         //   ->setContact($contactData)
                            ->setStatus(InviteStatusType::INVITE);
                    $em->getRepository('LocalsBest\UserBundle\Entity\Invite')->save($invite);

                    // Send Email about Invitation
                    $this->getMailMan()->sendInvitationMail($invite);
                }
                // Redirect user to another Page
                return $this->redirectToRoute('contact_view', array('id' => $contactData->getId()));
            }
        }
        // send params to view
        return [
            'form' => $form->createView(),
            'contactId' => $id,
            'contact' => $contact
        ];
    }

    /**
     * Display Contact Detail Page
     *
     * @Template
     *
     * @param Request $request
     * @param int $id
     * @throws Exception
     * @return array
     */
    public function viewAction(Request $request, $id)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        // disable soft delete, so app can show information from deleted users
        $em->getFilters()->disable('softdeleteable');
        // Get Contact by ID
        /** @var AllContact $contact */
        $contact = $this->findOr404('LocalsBestUserBundle:AllContact', array('id' => $id), 'Contact not found');
        // check User access to Contact
        /** @var boolean $accessToContact */
        $accessToContact = $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')
            ->checkContact($currentUser, $contact);
        if ( $accessToContact == false ) {
            throw  $this->createAccessDeniedException("You don't have permissions to access this contact.");
        }

        $member = null;
        $objectType = 'LocalsBestUserBundle:AllContact';

        // should system update Contact Pending Status
        if ($contact !== null && $contact->isPending() == true && $this->getUser() == $contact->getAssignTo()) {
            $contact->setIsPending(false);
            $em->flush();

            // send notification
            $this->get('localsbest.notification')
                ->addNotification(
                    'Contact "' . $contact->getFirstName() . ' ' . $contact->getLastName() . '" was checked by ' .
                    $currentUser->getFullName(),
                    'contact_view',
                    ['id' => $contact->getId()],
                    [$contact->getGeneratedBy()],
                    [$currentUser]
                );
        }

        $invite = $em->getRepository('LocalsBestUserBundle:Invite')->findOneBy(array('email' => $contact->getEmail()));
        
        $objectId = $id;
        $object = 'allContact';

        // Get Events for Contact
        $contactEvents = $this->getRepository('LocalsBest\UserBundle\Entity\Event')
            ->findEventsForContactOrUSer($objectId, $object);
        // Get Documents for Contact
        $contactDocuments = $this->getRepository('LocalsBest\UserBundle\Entity\DocumentUser')
            ->findDocumentsForContactOrUser($objectId, $object);
        
        $object = 'LocalsBestUserBundle:AllContact';
        $objectId = $contact;

        // Get Notes for Contact
        $notes = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
            ->findNotesForContactOrUser($object, $objectId);
        // Get Tags for Contact
        $tagsArray = array();
        $tags = $this->getRepository('LocalsBestCommonBundle:Tag')
            ->findTagsForContactOrUser('LocalsBestUserBundle:AllContact', $contact);
        
        foreach ($tags as $tagObject) {
            $tagsArray[] = $tagObject->getTag();
        }

        $tagForm = $this->createForm(TagType::class);
        $tagForm->get('tag')->setData(implode(', ', $tagsArray));

        $tagsAvailable = [];
        // Get users Business Entity
        $business = $currentUser->getBusinesses()->first();
        // tags system for Kelly
        if ($business->getid() == 155) {
            $owner = $business->getOwner();

            $tagsAvailable = $em->getRepository('LocalsBestCommonBundle:Tag')
                ->getUniqTags($owner, 'LocalsBestUserBundle:AllContact');
        }

        /** @var Business $business */
        $business = $this->getRepository('LocalsBestUserBundle:Business')
            ->find($this->get('session')->get('current_business'));

        // does current Business have ind type Real Estate Broker
        $REB = $em->getRepository('LocalsBestUserBundle:IndustryType')->find(23);
        if ($business->getTypes()->contains($REB)) {
            $allowInvite = true;
        } else {
            $allowInvite = false;
        }

        // send params to view
        return array(
            'file'              => $contact,
            'notes'             => $notes,
            'form'              => $tagForm->createView(),
            'objectType'        => $objectType,
            'object'            => $object,
            'objectId'          => $objectId,
            'tagsArray'         => $tagsArray,
            'contactDocuments'  => $contactDocuments,
            'contactEvents'     => $contactEvents,
            'member'            => $member,
            'tagsAvailable'     => $tagsAvailable,
            'allowInvite'       => $allowInvite,
        );
    }

    /**
     * Not sure, probably create Contact Entity from Member Entity
     *
     * @Template
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     * @return Response
     */
    public function addUserAjaxAction(Request $request, $id, $type) 
    {
        $em = $this->getDoctrine()->getManager();

        $business = null;

        // Get Business entity from Session
        if ($this->get('session')->get('current_business')) {
            $business = $this->getRepository('LocalsBestUserBundle:Business')->find($this->get('session')->get('current_business'));
        }

        // Get User Entity by ID
        $user  = $this->findOr404('LocalsBestUserBundle:User', array('id' => $id), 'User not found');
        if ($user) {
            if($type === 'Contact') {
                // Create new Contact Entity
                $contact = new AllContact();
                // Set information to Contact Entity from $user set it to current User
                $contact    ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                            ->setCreatedBy($this->getUser())
                            ->setFirstName($user->getFirstName())
                            ->setLastName($user->getLastName())
                            ->setUser($user);

                // Set phone number to Contact
                if ($user->getPrimaryPhone() !== null) {
                    $contact->setNumber($user->getPrimaryPhone()->getNumber());
                }
                // Set email address to Contact
                if ($user->getPrimaryEmail() !== null) {
                    $contact->setEmail($user->getPrimaryEmail()->getEmail());
                }
                // Save Contact
                $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);

                // Save Contact
                $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);

                /*** 27th may update on contact***/
                // Create second Contact Entity
                $secondcontact = new AllContact();
                // Set information to Contact Entity from current User set it to $user
                $secondcontact  ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                                ->setCreatedBy($user)
                                ->setFirstName($this->getUser()->getFirstName())
                                ->setLastName($this->getUser()->getLastName())
                                ->setUser($this->getUser());

                // Set phone number to Contact
                if ($this->getUser()->getPrimaryPhone() !== null) {
                    $secondcontact->setNumber($this->getUser()->getPrimaryPhone()->getNumber());
                }
                // Set email address to Contact
                if ($this->getUser()->getPrimaryEmail() !== null) {
                    $secondcontact->setEmail($this->getUser()->getPrimaryEmail()->getEmail());
                }
                // Save second Contact
                $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($secondcontact);

                // Redirect user to another Page
                return $this->redirect($this->generateUrl('contact_view', array('id' => $contact->getId())));
        
            } elseif ($type === 'User') {
                
                $createdBy = $this->getUser();
                
                if (count($createdBy->getOwnedBusiness()) > 0 ) {
                    foreach ($createdBy->getOwnedBusiness() as $business) {

                        if ($business->getStaffs()->contains($user)){
                            // Show flash message
                            $this->addErrorMessage('You already have this user in your member list.');
                            // Redirect User to prev Page
                            $referer = $request->headers->get('referer');
                            // Redirect User to prev Page
                            return $this->redirect($referer);
                        }
                        // Add $user to business
                        $user   ->addBusiness($business);
                        $business->addStaff($user);
                    }
                }else {
                    if(count($createdBy->getBusinesses()) > 0 ) {
                        foreach ($createdBy->getBusinesses() as $business) {

                            if ($business->getOwner()->getId() == $user->getId()){
                                // Show flash message
                                $this->addErrorMessage('You already have this user in your member list.');
                                // Redirect User to prev Page
                                $referer = $request->headers->get('referer');
                                // Redirect User to prev Page
                                return $this->redirect($referer);
                            }
                            // Add $user to business
                            $user   ->addBusiness($business);
                            $business->addStaff($user);
                        }
                    }
                }
                // Save changes
                $this->getDoctrine()->getManager()->persist($business);
                $this->getDoctrine()->getManager()->flush();

                // Create new Contact Entity
                $contact = new AllContact();
                // Set information to Contact Entity from current User
                $contact    ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                            ->setCreatedBy($user)
                            ->setFirstName($createdBy->getFirstName())
                            ->setLastName($createdBy->getLastName())
                            ->setNumber($createdBy->getPrimaryPhone()->getNumber())
                            ->setEmail($createdBy->getPrimaryEmail()->getEmail())
                            ->setUser($createdBy);

                $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);
                // Add Contact to $user Contact List
                $user->addAllContact($contact);
                $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                // Redirect user to another Page
                return $this->redirect($this->generateUrl('user_view', array('username' => $user->getUsername())));
            }
        }
    }

    /**
     * Send Free Invites for selected Contacts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function massInviteAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Contacts IDs from request
        $contactIds = $request->request->get('contacts', []);

        if ($contactIds !== []) {
            // Get Contacts List using IDs
            $contacts = $em->getRepository('LocalsBestUserBundle:AllContact')->findBy(['id' => $contactIds]);
            // Get Role Entity of Client
            $role = $em->getRepository('LocalsBestUserBundle:Role')->find(8);
            $counter = 0;

            /** @var AllContact $contact */
            foreach ($contacts as $contact) {
                // Is Contact attached to Member
                if ($contact->getUser() !== null) {
                    continue;
                }
                // Is Contact have email address
                if ($contact->getEmail() === null || $contact->getEmail() == '') {
                    continue;
                }

                // Create new Free Invite
                $invite = new Invite();

                // Set information to Invite
                $invite
                    ->setEmail($contact->getEmail())
                    ->setRole($role)
                    ->setToken(base64_encode(time() . ':' . rand()))
                    ->setCreatedBy($this->getUser())
                    ->setStatus(InviteStatusType::INVITE)
                    ->setContact($contact);

                $contact->setInvitation(true);
                $em->persist($invite);

                // Send Email with Free Invite
                $this->getMailMan()->sendInvitationMail($invite);
                $counter++;
            }
            // Save changes
            $em->flush();

            if ($counter == 0) {
                // send fail JSON
                return new JsonResponse([
                    'result'  => 'fail',
                    'status'  => 'danger',
                    'message' => 'System does not sent any invitations.'
                ]);
            }
            // send success JSON
            return new JsonResponse([
                'result'  => 'success',
                'status'  => 'success',
                'message' => 'System sent ' . $counter . ' from ' . count($contactIds) . ' invitations.',
            ]);
        }
        // send fail JSON
        return new JsonResponse([
            'result'  => 'fail',
            'status'  => 'danger',
            'message' => 'There was a problem on server'
        ]);
    }

    /**
     * Send Free Invite for Single Contact
     *
     * @param int $id
     * @param string $type
     * @return RedirectResponse
     */
    public function inviteAjaxAction($id, $type)
    {
        // Get object by ID
        $object = $this->findOr404($type, array('id' => $id), 'Invalid item');
        // Create Free Invite Entity
        $invite = new Invite();
        // Set information to Free Invite
        $invite->setEmail($object->getEmail());
        
        $token = base64_encode(time() . ':' . rand());
        $role = $this->getRepository('LocalsBestUserBundle:Role')->findOneBy(['name' => 'Client']);
        $invite->setToken($token)
                ->setRole($role)
                ->setCreatedBy($this->getUser())
                ->setStatus(InviteStatusType::INVITE);

        if ($type == "LocalsBestUserBundle:AllContact") {
            $invite->setContact($object);
        }

        $object->setInvitation(true);
        // Update object
        $this->getDoctrine()->getManager()->getRepository($type)->save($object);
        // Save Free Invite
        $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Invite')->save($invite);
        // Send Email with Free Invite
        $this->getMailMan()->sendInvitationMail($invite);
        // Redirect user to another Page
        return $this->redirect($this->generateUrl('contact_view', array('id' => $id)));
    }

    /**
     * Delete Contact
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Contact Entity by ID
        /** @var AllContact $contact */
        $contact = $this->findOr404('LocalsBestUserBundle:AllContact', array('id' => $id), 'Contact not found');
        /** @var ArrayCollection $contactList */
        $contactList = new ArrayCollection($em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->findAllContacts($this->getUser()));

        // Is current user generate this Contact
        if ($this->getUser() != $contact->getGeneratedBy()) {
            throw  $this->createAccessDeniedException("You don't have permissions to access this contact.");
        }

        // remove contacts Documents
        if ($contact->getDocuments()) {
            foreach ($contact->getDocuments() as $document) {
                $contact->removeDocument($document);
            }
        }
        // remove contacts Events
        if ($contact->getEvents()) {
            foreach($contact->getEvents() as $event) {
                $contact->removeEvent($event);
            }
        }
        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');
        // Remove Contact
        $em->remove($contact);
        // Save changes
        $em->flush();
        // Redirect user to another Page
        return $this->redirect($this->generateUrl('contact_index'));
    }

    /**
     * No idea
     *
     * @param string $email
     * @return RedirectResponse
     */
    public function shareResponseAction($email)
    {
        // Get Email Entity by email address
        $emailExists = $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Email')->findOneByEmail($email);
                
        if ($emailExists) {
            $user = $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(array('primaryEmail' => $emailExists));
        }
        
        if ($user->getRole()->getLevel() > $this->getUser()->getRole()->getLevel()) {
            // Redirect user to another Page
            return $this->redirect($this->generateUrl('user_view', array('username' => $user->getUsername())));
        } else {
            // Get Contact Entity by email address
            $contact = $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\AllContact')->findOneBy(array('email' => $email));
            // Redirect user to another Page
            return $this->redirect($this->generateUrl('contact_view', array('id' => $contact->getId())));
        }
    }

    /**
     * Search Contacts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function jsonAction(Request $request)
    {
        $matching = [];
        
        $query = $request->query->get('query', null);
        
        if ($query) {
            $matching['firstName']      = $query;
            $matching['lastName']       = $query;
            $matching['email']          = $query;
            $matching['businessEmail']  = $query;
            $matching['businessName']  = $query;
        }
        // Get current Business Staff
        $users = $this->getStaffs();
        // Get Contacts for current user
        $contacts = $this->getRepository('LocalsBest\UserBundle\Entity\AllContact')->findMyObjects($this->getUser(), $users, $matching);
        
        $jsonPayload = array();
        
        $uploadHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        // Prepare JSON Response
        foreach ($contacts as $contact) {
            $jsonPayload[] = [
                'id'                => $contact->getId(),
                'fullname'          => $contact->getFullName(),
                'email'             => $contact->getEmail(),
                'phone'             => $contact->getNumber(),
                'businessName'      => $contact->getBusinessName(),
                'profilepic'        => 'http://www.placehold.it/64x64/EFEFEF/AAAAAA&text=no+image'
            ];
        }
        
        foreach ($this->getStaffs(['firstName' => $query,'lastName' => $query]) as $staff) {
            $jsonPayload[] = [
                'id'                => $staff->getId(),
                'fullname'          => $staff->getFullName(),
                'email'             => $staff->getPrimaryEmail() ? $staff->getPrimaryEmail()->getEmail() : '- no email -',
                'phone'             => $staff->getPrimaryPhone() ? $staff->getPrimaryPhone()->getNumber() : '- no phone -',
                'businessName'      => $this->getBusiness()->getName(),
                'profilepic'        => $staff->getFileName()
                    ? $uploadHelper->asset($staff, 'file')
                    : 'http://www.placehold.it/64x64/EFEFEF/AAAAAA&text=no+image',
            ];
        }
        
        return new JsonResponse($jsonPayload);
    }

    /**
     * Add Document to Contact
     *
     * @Template
     *
     * @param Request $request
     * @param int $id
     * @return array|RedirectResponse
     */
    public function documentAddAction(Request $request, $id)
    {
        // Get Contact Entity by ID
        $contact = $this->findOr404('LocalsBestUserBundle:AllContact', array('id' => $id), 'Invalid item');
        // Create new Contact Document Entity
        $document = new DocumentUser();
        $document->setStatus($this->getRepository('LocalsBestUserBundle:Document')->getDefaultStatus());
        $user = $this->getUser();
        // Create Contact Document Form object
        $documentForm = $this->createForm(DocumentUserType::class, $document);
        // Set request information to Contact Document Form
        $documentForm->handleRequest($request);

        // If Contact Document Form was submit
        if ($request->isMethod("POST")) {
            if ($documentForm->isValid()) {
                // Create Contact Document Entity from Contact Document Form information
                $document = $documentForm->getData();
                // Get current Business Entity
                $business = $this->getRepository('LocalsBestUserBundle:Business')->find($this->get('session')->get('current_business'));
                // Set information for Contact Document Entity
                $document->setOwner($business)
                    ->setCreatedBy($user)
                    ->setallContact($contact);
                // Save
                $this->getRepository('LocalsBestUserBundle:DocumentUser')->save($document);
                // Redirect to prev page
                return $this->redirect($request->server->get('HTTP_REFERER'));
            }

            if ($documentForm->isValid() === false) {
                $res = [];
                // Get errors messages
                foreach ( $documentForm->getErrors(true) as $error ) {
                   $res[] = $error->getMessage();
                }
                // Show flash message
                $this->addFlash('danger', implode('<br>', $res));
                // Redirect to prev page
                return $this->redirect($request->server->get('HTTP_REFERER'));
            }
        }
        // Sent info to View
        return array('documentForm' => $documentForm->createView(), 'id' => $id);
    }

    /**
     * Display Page for Contacts import
     *
     * @Template
     */
    public function importAction()
    {
        return [];
    }

    /**
     * Import Contacts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxImportAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            // process take a lot of time
            set_time_limit(0);
            // Get field info from request
            $fields = $request->request->all();
            // Get uploaded file
            $file = $request->files->get('importFile');
            // Check extension for error
            if ($file === null) {
                return new JsonResponse('File was not uploaded.', 400);
            }
            // Get file extension
            $fileExt = $file->getClientOriginalExtension();
            // Check extension for error
            if (!in_array(strtolower($fileExt), ['csv', 'xls', 'xlsx'])) {
                return new JsonResponse('Wrong File Extension', 400);
            }

            $em = $this->getDoctrine()->getManager();
            $batchSize = 100;
            
            switch ($fields['fileType']) {
                case 'csv':
                    $row = 0;
                    if (($handle = fopen($file, "r")) !== FALSE) {
                        
                        $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
                        while (($data = fgetcsv($handle, $max_line_length)) !== FALSE) {
                            
                            $row++;

                            if ($row == 1) {
                                continue;
                            }
                            
                            // Search Contact in DB
                            if(isset($data[$fields['firstName'] - 1]) && isset($data[$fields['lastName'] - 1]) && isset($data[$fields['email'] - 1]) && isset($data[$fields['phone'] - 1])){
                                 
                            $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy([
                                'createdBy' => $this->getUser(),
                                'firstName' => $data[$fields['firstName'] - 1] != '' ? $data[$fields['firstName'] - 1] : '',
                                'lastName' => $data[$fields['lastName'] - 1] != '' ? $data[$fields['lastName'] - 1] : '',
                                'email' => $data[$fields['email'] - 1] != '' ? $data[$fields['email'] - 1] : '',
                                'number' => $data[$fields['phone'] - 1] != '' ? $data[$fields['phone'] - 1] : '',
                            ]);
                            }
                            else{
                               return new JsonResponse('Wrong Date in File', 400);
                            }
                           
                            // if Contact not exist with params create it
                            if (is_null($contact)) {
                                $contact = new AllContact();
                                $contact->setFirstName($data[$fields['firstName'] - 1] != '' ? $data[$fields['firstName'] - 1] : '');
                                $contact->setLastName($data[$fields['lastName'] - 1] != '' ? $data[$fields['lastName'] - 1] : '');
                                $contact->setNumber($data[$fields['phone'] - 1] != '' ? $data[$fields['phone'] - 1] : '');
                                $contact->setEmail($data[$fields['email'] - 1] != '' ? $data[$fields['email'] - 1] : '');
                                $contact->setCreatedBy($this->getUser());

                                $em->persist($contact);
                                $em->flush();
                            }

                            // Check note for import
                            if ($fields['note'] != '') {
                                // Create new Note for Contact
                                $note = new Note();
                                $note->setCreatedBy($this->getUser());
                                $note->setNote($data[$fields['note'] - 1]);
                                $note->setObjectType('LocalsBestUserBundle:AllContact');
                                $note->setObjectId($contact->getId());
                                $note->setPrivate(true);
                                $contact->addNote($note);

                                $em->persist($note);
                                $em->flush();
                            }

                            if (($row % $batchSize) === 0) {
                                // Detaches all objects from Doctrine!
                                $em->clear($contact);
                            }
                        }
                        // Close file
                        fclose($handle);
                    }
                    
                    // return JSON
                    return new JsonResponse(['count' => $row-1]);
                break;
                case 'excel':
                    $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($file);
                    //  Get worksheet dimensions
                    $sheet = $phpExcelObject->getSheet(0);
                    $highestRow = $sheet->getHighestRow();

                    //  Loop through each row of the worksheet in turn
                    for ($row = 2; $row <= $highestRow; $row++) {
                        //  Read a row of data into an array
                        $rowDataSet = $sheet->rangeToArray('A' . $row . ':' . 'AZ' . $row, NULL, TRUE, FALSE);
                        $rowData = $rowDataSet[0];

                        // Search Contact in DB
                        $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy([
                            'createdBy' => $this->getUser(),
                            'firstName' => $rowData[$fields['firstName'] - 1] != '' ? $rowData[$fields['firstName'] - 1] : '',
                            'lastName' => $rowData[$fields['lastName'] - 1] != '' ? $rowData[$fields['lastName'] - 1] : '',
                            'email' => $rowData[$fields['email'] - 1] != '' ? $rowData[$fields['email'] - 1] : '',
                            'number' => $rowData[$fields['phone'] - 1] != '' ? $rowData[$fields['phone'] - 1] : '',
                        ]);

                        if (is_null($contact)) {
                            // Create new Contact
                            $contact = new AllContact();
                            $contact->setFirstName($rowData[$fields['firstName'] - 1] != '' ? $rowData[$fields['firstName'] - 1] : '');
                            $contact->setLastName($rowData[$fields['lastName'] - 1] != '' ? $rowData[$fields['lastName'] - 1] : '');
                            $contact->setNumber($rowData[$fields['phone'] - 1] != '' ? $rowData[$fields['phone'] - 1] : '');
                            $contact->setEmail($rowData[$fields['email'] - 1] != '' ? $rowData[$fields['email'] - 1] : '');
                            $contact->setCreatedBy($this->getUser());

                            $em->persist($contact);
                            $em->flush();
                        }

                        // Check note for import
                        if ($fields['note'] != '') {
                            // Create new Note for Contact
                            $note = new Note();
                            $note->setCreatedBy($this->getUser());
                            $note->setNote($rowData[$fields['note'] - 1]);
                            $note->setObjectType('LocalsBestUserBundle:AllContact');
                            $note->setObjectId($contact->getId());
                            $note->setPrivate(true);
                            $contact->addNote($note);

                            $em->persist($note);
                            $em->flush();
                        }

                        if (($row % $batchSize) === 0) {
                            // Detaches all objects from Doctrine!
                            $em->clear($contact);
                        }
                    }
                    // return JSON
                    return new JsonResponse(['count' => $row-1]);
                break;
            }
        }
        die('403');
    }

    /**
     * Display window for assign to Contact
     *
     * @Template
     *
     * @param Request $request
     * @return Response
     */
    public function assignWindowAction(Request $request)
    {
        $staff = null;
        $single = null;

        // Check role of current user
        if ($this->getUser()->getRole()->getLevel() < 7) {
            // get Business Staff
            $staff = $this->getRepository('LocalsBestUserBundle:User')->findFullStaffs(
                $this->getUser()->getBusinesses()->first()->getOwner(),
                $this->getUser()->getBusinesses()->first()
            );
        }

        // Get Contact ID from request
        $contactId = $request->query->get('contact', null);
        $member_events = [];
        $member_documents = [];
        $member_notes = [];

        if (null !== $contactId) {
            $single = 100;
            // Get Contact Entity by ID
            $contact = $this->getDoctrine()->getManager()->getRepository('LocalsBestUserBundle:AllContact')->find($contactId);

            if(null !== $contact && null !== $contact->getUser()) {
                $user = $contact->getUser();
                // Get Contact Member Events
                $member_events = $this->getRepository('LocalsBest\UserBundle\Entity\Event')->findMyEvents($this->getUser(), $user, 'user');
                // Get Contact Member Documents
                $member_documents = $this->getRepository('LocalsBest\UserBundle\Entity\DocumentUser')->findMyDocuments($this->getUser(), $user, 'user');

                $myStaffs = $this->getStaffs();
                // Get Contact Member Notes
                $member_notes = $this->getRepository('LocalsBest\CommonBundle\Entity\Note')->findMyNotes($this->getUser(), 'LocalsBestUserBundle:User', $user, $myStaffs);
            }
        }
        // Render view with params
        return [
            'single'            => $single,
            'contactId'         => $contactId,
            'team'              => $staff,
            'member_events'     => $member_events,
            'member_documents'  => $member_documents,
            'member_notes'      => $member_notes,
        ];
    }

    /**
     * Action will assign contacts to chosen agent
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assignContactsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agent = null;
        $contacts = null;
        // Get Agent ID from request
        $agentId = $request->request->get('agent_id', null);
        // Get Contacts IDs from request
        $contactIds = $request->request->get('contact_ids', []);

        if ($agentId !== null) {
            // Get User Entity using agent ID
            $agent = $em->getRepository('LocalsBestUserBundle:User')->find($agentId);
        }

        if ($contactIds !== []) {
            // Get Contacts Entities
            $contacts = $em->getRepository('LocalsBestUserBundle:AllContact')->findBy(['id' => $contactIds]);
        }

        if($agent !== null) {
            $successContacts = [];
            $counter = 0;
            /** @var AllContact $contact */
            foreach ($contacts as $contact) {

                if ($contact->getUser() !== null) {
                    $counter++;
                    continue;
                }
                // Reassign Contact to Agent
                $contact->setAssignTo($agent);
                $contact->setIsPending(true);

                $this->sendWpRequest($agent, $contact->getWpId());

                $successContacts[] = $contact->getId();
            }
            $em->flush();

            // Create message for agent
            $message = "You have " . count($successContacts) . " new contact(s) from " . $this->getUser()->getFullName() . ".";
            // Send Notification for agent
            $this->get('localsbest.notification')
                ->addNotification($message, 'contact_index', array(), array($agent), array($this->getUser()));

            if ($agent->getPreference()->getMail() === TRUE) {
                // Send Mail for agent
                $this->getMailMan()->assignContactsMail($agent, $message);
            }

            if ($agent->getPreference()->getSms() === TRUE) {
                // Send SMS for agent
                $phone  = $agent->getPrimaryPhone()->getNumber();
                $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                $sender = $this->get('jhg_nexmo_sms');
                $sender->sendText('+1' . $number, $message, null);
            }
            // Return success JSON
            return new JsonResponse([
                'result' => 'success',
                'success_results' => $successContacts,
                'message' => $counter == 0
                    ? 'Contacts assign successfully'
                    : 'You must transfer member records 1 at a time from the detail page.',
                'status' => $counter > 0 ? 'warning' : 'success',
                'icon' => $counter > 0 ? 'warning' : 'check',
            ]);
        }

        return new JsonResponse(['result' => 'fail']);
    }

    /**
     * Action will assign single contact to chosen agent
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function assignSingleContactAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $agent = null;
        $contacts = null;
        // Get agent ID from request
        $agentId = $request->request->get('agent_id', null);
        // Get contacts IDs from request
        $contactIds = $request->request->get('contact_ids', []);
        // Get selected documents for reassign from request
        $documents_ids = $request->request->get('documents', []);
        $documents = $em->getRepository('LocalsBestUserBundle:DocumentUser')->findBy(['id' => $documents_ids]);
        // Get selected notes for reassign from request
        $notes_ids = $request->request->get('notes', []);
        $notes = $em->getRepository('LocalsBestCommonBundle:Note')->findBy(['id' => $notes_ids]);
        // Get selected events for reassign from request
        $events_ids = $request->request->get('events', []);
        $events = $em->getRepository('LocalsBestUserBundle:Event')->findBy(['id' => $events_ids]);

        if ($agentId !== null) {
            // Get User Entity for selected agent
            $agent = $em->getRepository('LocalsBestUserBundle:User')->find($agentId);
        }

        if ($contactIds !== []) {
            $contacts = $em->getRepository('LocalsBestUserBundle:AllContact')->findBy(['id' => $contactIds]);
        }

        if ($agent !== null) {
            $result['contacts'] = [];
            /** @var AllContact $contact */
            foreach ($contacts as $contact) {
                // Reassign Contact
                $contact->setAssignTo($agent);
                $contact->setIsPending(true);

                // Duplicate information from Member to Contact
                /** @var Note $item */
                foreach ($notes as $item) {
                    $note = clone $item;
                    $note->setObjectType('LocalsBestUserBundle:AllContact')->setObjectId($contact->getId())->setSlug($item->getSlug() . '-' . rand(2, 1001));
                    $contact->addNote($note);
                    $em->persist($note);
                }

                /** @var Event $item */
                foreach ($events as $item) {
                    $event = clone $item;
                    $event->setAllContact($contact)->setUser(null)->setSlug($item->getSlug() . '-' . rand(2, 1001));
                    $em->persist($event);
                }

                /** @var DocumentUser $item */
                foreach ($documents as $item) {
                    $document = clone $item;
                    $document->setAllContact($contact)->setUser(null)->setSlug($item->getSlug() . '-' . rand(2, 1001));
                    $em->persist($document);
                }

                $this->sendWpRequest($agent, $contact->getWpId());
            }
            $em->flush();
            $result['status'] = 'success';

            // Send Notification
            $this->get('localsbest.notification')
                ->addNotification("You have new contact from " . $this->getUser()->getFullName() . ".", 'contact_index', array(), array($agent), array($this->getUser()));

            // Show flash message
            $this->addFlash('success', 'Contact successfully assigned to ' . $agent->getFullName());
            // Redirect user to prev Page
            $referer = $request->headers->get('referer');
            // Redirect user to prev Page
            return $this->redirect($referer);
        }
        // Show flash message
        $this->addFlash('danger', 'Sorry, there was problem with server.');
        // Redirect user to prev Page
        $referer = $request->headers->get('referer');
        // Redirect user to prev Page
        return $this->redirect($referer);
    }

    /**
     * Action will assign contacts Back
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assignContactsToLeaderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Contacts IDs from request
        $contactIds = $request->request->get('contact_ids', []);

        if ($contactIds !== []) {
            $contacts = $em->getRepository('LocalsBestUserBundle:AllContact')->findBy(['id' => $contactIds]);

            /** @var AllContact $contact */
            foreach ($contacts as $contact) {
                $contact->setAssignTo(null);
                $contact->setIsPending(false);

                $this->sendWpRequest($contact->getGeneratedBy(), $contact->getWpId());
                // Create message for system notifications
                $message = $this->getUser()->getFullName() . ' return contact "' . $contact->getFullName() . '" back.';

                if ($this->getUser() != $contact->getGeneratedBy()) {
                    // Send Notification
                    $this->get('localsbest.notification')
                        ->addNotification(
                            $message,
                            'contact_view',
                            ['id' => $contact->getId()],
                            [$contact->getGeneratedBy()],
                            array($this->getUser())
                        )
                    ;

                    if ($contact->getGeneratedBy()->getPreference()->getMail() === TRUE) {
                        // Send Mail
                        $this->getMailMan()->assignContactsMail($contact->getGeneratedBy(), $message);
                    }
                }
            }
            // Save changes
            $em->flush();
            $result['status'] = 'success';
            // Return success JSON
            return new JsonResponse($result);
        }
        // Return fail JSON
        return new JsonResponse(['result' => 'fail']);
    }

    /**
     * Action will change contact category
     *
     * @param Request $request
     * @param int $contactId
     * @return JsonResponse
     */
    public function changeCategoryAjaxAction(Request $request, $contactId)
    {
        $em = $this->getDoctrine()->getManager();
        // List of categories
        $categories = ['', 'buyer', 'seller', 'tenant', 'landlord'];
        // selected category
        $category = $request->request->get('category');
        // Get Contact Entity by ID
        /** @var AllContact $contact */
        $contact = $em->getRepository("LocalsBestUserBundle:AllContact")->find($contactId);

        if ($contact !== null && in_array($category, $categories)) {
            // Set new Category for Contact
            $contact->setCategory($category);
            // Save changes
            $em->flush();
            // Return success JSON
            return new JsonResponse(['result' => 'success']);
        }
        // Return fail JSON
        return new JsonResponse(['result' => 'fail']);
    }

    /**
     * Action will change contact type
     *
     * @param Request $request
     * @param int $contactId
     * @return JsonResponse
     */
    public function changeTypeAjaxAction(Request $request, $contactId)
    {
        $em = $this->getDoctrine()->getManager();
        // List of types
        $types = ['lead', 'contact'];
        // selected type
        $type = $request->request->get('type');
        // Contact Entity
        /** @var AllContact $contact */
        $contact = $em->getRepository("LocalsBestUserBundle:AllContact")->find($contactId);

        if ($contact !== null && in_array($type, $types)) {
            // set new type for Contact
            $contact->setType($type);
            // Save changes
            $em->flush();
            // Return success JSON
            return new JsonResponse(['result' => 'success']);
        }
        // Return fail JSON
        return new JsonResponse(['result' => 'fail']);
    }

    /**
     * Action will change contact active status
     *
     * @param Request $request
     * @param int $contactId
     * @return JsonResponse
     */
    public function changeIsActiveAjaxAction(Request $request, $contactId)
    {
        $em = $this->getDoctrine()->getManager();
        // selected active status
        $isActive = $request->request->get('is_active');
        // Contact Entity
        /** @var AllContact $contact */
        $contact = $em->getRepository("LocalsBestUserBundle:AllContact")->find($contactId);
        // Set new active status
        $contact->setIsActive(($isActive == 1 ? true : false));
        // save changes
        $em->flush();
        // Return success JSON
        return new JsonResponse(['result' => 'success']);
    }

    /**
     * Display page for print Contact information
     *
     * @Template
     *
     * @param Request $request
     * @return Response
     */
    public function printAction(Request $request)
    {
        // selected contacts ids
        $contactIds = $request->request->get('contact_ids', '');
        $em = $this->getDoctrine()->getManager();

        // disable soft delete, so app can show information from deleted users
        $em->getFilters()->disable('softdeleteable');

        if ($contactIds === '') {
            return new JsonResponse([]);
        }
        // Get Contacts Entities
        $contacts = $em->getRepository('LocalsBestUserBundle:AllContact')->findForPrint(explode(',', $contactIds));
        
        // Render View with contacts
        return [
            'contacts' => $contacts
        ];
    }

    private function sendWpRequest(User $user, $contactId)
    {
        $url = $user->getWpWebsiteUrl()
            . (substr($user->getWpWebsiteUrl(), -1) == '/' ? '' : '/')
            . 'wp-json/ec_api/v1/reassigning_contact_to_another_agent';

        $username = "basil";
        $password = "147896325";

        $token = base64_encode($username . ':' . $password);

        $headers = ['Authorization: Basic ' . $token];

        $params = [
            'client_id' => $contactId,
            'new_user_id' => $user->getWpAgentId(),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        $resultJson = curl_exec($ch); // run the whole process

        if($resultJson == false) {
            return false;
        }

        curl_close($ch);

        return true;
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function businessInfoAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var AllContact $contact */
        $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->find($id);

        if($request->isMethod("POST")) {
            $data = $request->request->all();

            $contact->setBusinessName($data['business_name']);
            $contact->setBusinessEmail($data['business_email']);
            $contact->setBusinessPhone($data['business_phone']);
            $contact->setBusinessAddressStreet($data['business_street']);
            $contact->setBusinessAddressUnit($data['business_unit']);
            $contact->setBusinessAddressCity($data['business_city']);
            $contact->setBusinessAddressState($data['business_state']);
            $contact->setBusinessAddressZip($data['business_zip']);

            $em->flush();
            return $this->redirectToRoute('contact_view', ['id' => $contact->getId()]);
        }

        return [
            'contact' => $contact
        ];
    }
}