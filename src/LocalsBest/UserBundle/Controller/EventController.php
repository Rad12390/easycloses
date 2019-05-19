<?php

namespace LocalsBest\UserBundle\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\EventTypeType;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\EventType;
use LocalsBest\UserBundle\Form\NoteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{

    /**
     * Lists all events for current user.
     *
     * @Template
     * @return array|RedirectResponse
     */
    public function indexAction()
    {
        // Check user password for default values
        if (!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        if (!$this->get('localsbest.checker')->forAddon('events & alerts', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        // Disable Soft Delete Filter
        $this->getDoctrine()->getManager()->getFilters()->disable('softdeleteable');

        return [];
    }

    /**
     * Create or Edit event
     *
     * @param Request $request
     * @param string $slug
     * @param string $objectType
     * @param int $objectId
     *
     * @return array|RedirectResponse|Response
     * @Template
     */
    public function editAction(Request $request, $slug = null, $objectType = null, $objectId = null)
    {
        // Check user password for default values
        if (!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        if (!$this->get('localsbest.checker')->forAddon('events & alerts', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $accessEdit = null;
        $custom = null;
        $sharedWithUsernames = array();
        $assignWithUsernames = array();
        $eventId = null;
        $object = null;

        // Get object by type and id
        if ($objectId != null && $objectType != null) {
            $object = $this->findOr404($objectType, array('id' => $objectId));
        }

        $em = $this->getDoctrine()->getManager();

        if (!empty($slug)) {
            // Get Business Staff
            if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
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

            // Get Event by slug
            /** @var Event $event */
            $event = $this->findOr404(
                'LocalsBestUserBundle:Event',
                array('slug' => $slug),
                'No such event found'
            );
            // Check current user permission for current event
            if (
                $event->getCreatedBy() != $this->getUser()
                && !in_array($event->getCreatedBy(), $users)
            ) {
                throw $this->createAccessDeniedException('Access Denied');
            }

            if (
                !is_null($object)
                && $objectType == 'LocalsBestUserBundle:Job'
                && $object->getVendor()->getId() !== $this->getUser()->getId()
            ) {
                throw $this->createAccessDeniedException('Access Denied');
            }
            
            $eventId = $event->getId();
            $accessEdit  = true;
            $user = $event->getCreatedBy();
            $type = $event->getType();

            if (!in_array($type, array('Task','Call','custom','Email','Appoinment'))){
                $custom = $type ;
            }
            // Disable Soft Delete Filter
            $em->getFilters()->disable('softdeleteable');
            foreach ($event->getShares() as $share) {
                // get shared users for current event
                $sharedWithUsernames[] = $share->getUser()->getUsername();
            }

            foreach ($event->getAssignedTo() as $assignTo) {
                // get users that assigned for current event
                $assignWithUsernames[] = $assignTo->getUsername();
            }
        } else {
            $accessEdit  = false;
            // Create event Entity
            $event = new Event();
            $event->setStatus($this->getRepository('LocalsBestUserBundle:Event')->getOpenStatus());
            $user = $this->getUser();
        }

        //  Get Event Form object
        $form = $this->createForm(EventType::class, $event);
        // if form was submit
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = $form->getData();

                // Set Information for Event
                if ($event->getType() === EventTypeType::CUSTOM) {
                    $event->setCustom($form->get('custom')->getData());
                }

                if ($eventId !== null) {
                    if($form->get('type')->getData() != null) {
                        $event->setType($form->get('type')->getData());
                    } else {
                        $event->setType($type);
                    }
                } else {
                    $event->setType($form->get('type')->getData());
                }

                $event->setTime($form->get('time')->getData());
                $business = null;

                if ($this->get('session')->get('current_business')) {
                    $business = $this->getRepository('LocalsBestUserBundle:Business')
                        ->find($this->get('session')->get('current_business'));
                }

                if ($business != null) {
                    $event->setOwner($business);
                }

                $event->setCreatedBy($user);

                // Set Alerts for Event
                if (count($event->getAlerts()->toArray())) {
                    foreach ($event->getAlerts() as $alert) {
                        $alert->setEvent($event);
                        $em->persist($alert);
                        $event->addAlert($alert);
                        $event->setAlert(true);
                    }
                }

                // Get fields that were updated
                $uow = $em->getUnitOfWork();
                $uow->computeChangeSets();
                $changeSet = $uow->getEntityChangeSet($event);



                if ($event->getId() === null) {
                    $em->persist($event);
                }

                $em->flush();

                $postData = $request->request->all();
                $assign_usernames = $postData['assignedTo'];
                $assign_usernames = explode(', ', $assign_usernames);
                $assign_usernames = array_unique($assign_usernames);

                // Assign Event to Users
                foreach ($assign_usernames as $username) {
                    if (empty(trim($username))) {
                        continue;
                    }

                    $user = $this->getRepository('LocalsBest\UserBundle\Entity\User')
                        ->findOneBy(array('username' => $username));
                    if (!$user) {
                        continue;
                    }
                    $event->addAssignedTo($user);
                }

                // Condition for Event share
                switch ($postData['shareType']) {
                    case 'team':
                        $share_usernames = $this->getUser()->getTeam()->getAgents();
                        break;
                    case 'company':
                        $share_usernames =$this->getRepository('LocalsBest\UserBundle\Entity\User')
                            ->findFullStaffs($this->getUser(), $this->getBusiness());
                        break;
                    case 'staff':
                        $share_usernames = $this->getRepository('LocalsBest\UserBundle\Entity\User')
                            ->findStaff($this->getUser(), $this->getBusiness());
                        break;
                    case 'non-staff':
                        $share_usernames = $this->getRepository('LocalsBest\UserBundle\Entity\User')
                            ->findNonStaff($this->getUser(), $this->getBusiness());
                        break;
                    case 'team_and_staff':
                        $share_usernames = new ArrayCollection(
                            array_merge(
                                $this->getUser()->getTeam()->getAgents()->toArray(),
                                $this->getRepository('LocalsBest\UserBundle\Entity\User')
                                    ->findStaff($this->getUser(), $this->getBusiness())
                            )
                        );
                        break;
                    case 'vendors':
                        $share_usernames = $this->getUser()->getMyVendors();
                        break;
                    case 'clients':
                        $share_usernames = $this->getRepository('LocalsBest\UserBundle\Entity\User')
                            ->findClients($this->getUser(), $this->getBusiness());
                        break;
                    case 'custom':
                        $share_usernames = $postData['shares'];
                        $share_usernames = explode(', ', $share_usernames);
                        $share_usernames = array_unique($share_usernames);
                        break;
                    default:
                        $share_usernames = [];
                }
                $sharedWithUsers = array();
                $message = 'New Event shared with you';

                foreach ($share_usernames as $username) {
                    if(!$username instanceof User) {
                        if (empty(trim($username))) {
                            continue;
                        }
                        $user = $this->getRepository('LocalsBest\UserBundle\Entity\User')
                            ->findOneBy(array('username' => $username));
                        if (!$user) {
                            continue;
                        }
                    } else {
                        $user = $username;
                    }
                    if($user == $this->getUser()) {
                      continue;
                    }

                    $this->getRepository('LocalsBest\UserBundle\Entity\Event')->save($event);
                    // create share process
                    $share = new Share();
                    $share->setUser($user);

                    $time = time();
                    $randNum = rand();
                    $token = base64_encode($time . ':' . $randNum);
                    $share->setToken($token);
                    $share->setCreatedBy($this->getUser());
                    $share
                        ->setObjectType(ObjectTypeType::Event)
                        ->setObjectId($event->getId())
                    ;

                    $event->addShare($share);
                    $sharedWithUsers[] = $user;

                    if ($user->getPreference()->getMail() === TRUE) {
                        // Send Email about this
                        $this->getMailMan()->sendSharedMail($share);
                    }

                    if ($user->getPreference()->getSms() === TRUE && count($share_usernames) == 1 ) {
                        // Send SMS about this
                        $phone = $user->getPrimaryPhone()->getNumber();
                        $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                        $sender = $this->get('jhg_nexmo_sms');
                        $sender->sendText('+1' . $number, $message, null);
                    }
                }

                // Send Notification about this
                $this->get('localsbest.notification')
                    ->addNotification(
                        $message,
                        'event_view',
                        ['slug' => $event->getSlug()],
                        $sharedWithUsers,
                        [$this->getUser()]
                    )
                ;

                // Show flash message
                $this->addSuccessMessage($event->getTitle() . ' updated successfully!');


                if (!empty($changeSet)) {

                    $userList = [];

                    if ($event->getCreatedBy()->getId() != $this->getUser()->getId()) {
                        $userList[] = $this->getUser();
                    }

                    foreach ($event->getShares() as $share) {
                        if ($share->getUser() == $this->getUser()) {
                            continue;
                        }

                        $userList[] = $share->getUser();
                    }

                    $this->get('localsbest.notification')
                        ->addNotification(
                            'Event was updated',
                            'event_view',
                            array('slug' => $event->getSlug()),
                            $userList,
                            array($this->getUser())
                        )
                    ;

                }

                if ($object != null && $objectType === 'LocalsBestUserBundle:Transaction') {
                    $event->setTransaction($object);
                    $object->addEvent($event);
                    $this->getRepository('LocalsBestUserBundle:Event')->save($event);
                    $em->getRepository('LocalsBest\UserBundle\Entity\Transaction')->save($object);
                    return $this->redirect($this->generateUrl('transaction_view', array('id' => $objectId)));
                } elseif ($object != null && $objectType === 'LocalsBestUserBundle:Job') {
                    $event->setJob($object);
                    $object->addEvent($event);
                    $this->getRepository('LocalsBestUserBundle:Event')->save($event);
                    $em->getRepository('LocalsBest\UserBundle\Entity\Job')->save($object);
                    return $this->redirect($this->generateUrl('job_view', array('id' => $objectId)));
                } elseif ($object != null && $objectType === 'LocalsBestUserBundle:AllContact') {
                    $event->setAllContact($object);
                    $object->addEvent($event);
                    $this->getRepository('LocalsBestUserBundle:Event')->save($event);
                    $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($object);
                    return $this->redirect($this->generateUrl('contact_view', array('id' => $objectId)));
                } elseif ($object != null && $objectType === 'LocalsBestUserBundle:User') {
                    $event->setUser($object);
                    $object->addEvent($event);
                    $this->getRepository('LocalsBestUserBundle:Event')->save($event);
                    $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($object);
                    return $this->redirect(
                        $this->generateUrl('user_view', array('username' => $object->getUsername()))
                    );
                } else {
                    $this->getRepository('LocalsBestUserBundle:Event')->save($event);
                    return $this->redirectToRoute('event_index');
                }
            }
        }

        // Render View according to condition
        if(true || $accessEdit === TRUE) {
            return array(
                'form' => $form->createView(),
                'slug' => $slug ? $slug : 'new',
                'accessEdit' => $accessEdit,
                'file' => $event,
                'sharedwithusernames' => implode(',', $sharedWithUsernames) . ',',
                'assignwithusernames' => implode(',', $assignWithUsernames) . ',',
                'objectId' => $objectId,
                'objectType' => $objectType
            );
        } else {
            return $this->render(
                '@LocalsBestUser/event/add.html.twig',
                array(
                    'form' => $form->createView(),
                    'slug' => $slug ? $slug : 'new',
                    'objectId' => $objectId,
                    'objectType' => $objectType
                )
            );
        }
    }

    /**
     * View Event information
     *
     * @param string $slug
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function viewAction($slug)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        if (!$this->get('localsbest.checker')->forAddon('events & alerts', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');

        $sharedWithUsernames = array();

        // Get Event by slug
        /** @var Event $event */
        $event = $this->findOr404('LocalsBestUserBundle:Event', array('slug' => $slug), 'Event not found');

        if ($event->getStatus() === null || !in_array($event->getStatus()->getId(), [3, 5])) {
            $event->setStatus($em->getReference('LocalsBestCommonBundle:Status', 3));
            $em->flush();
        }

        if (
            $event->getStatus() !== null
            && $event->getStatus()->getStatus() === 'new'
            && $event->getEventStatus() !== null &&
            $event->getEventStatus() === false
        ) {
            $event->setEventStatus(TRUE);
            $this->getRepository('LocalsBestUserBundle:Event')->save($event);
        } elseif (
            $event->getStatus() !== null
            && $event->getStatus()->getStatus() === 'new'
            && $event->getEventStatus() === null
        ) {
            $event->setEventStatus(TRUE);
            $this->getRepository('LocalsBestUserBundle:Event')->save($event);
        } elseif ($event->getStatus() === null) {
            $event->setStatus($this->getRepository('LocalsBestCommonBundle:Status')->findOneBy(['status' => 'new']));
        }

        if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $users = array();
            $businesses = $this->getUser()->getBusinesses();
           
            foreach($businesses as $business) {
                $user = $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\User')
                   ->findOneBy(array('id' => $business->getOwner()->getid()));
                $users[] = $user;
            }
        } else {
            $users = $this->getStaffs();
        }

        if ($this->getUser()->isDocumentApprover()) {
            $u = $this->getBusiness()->getOwner();
        } else {
            $u = $this->getUser();
        }

        if (
            in_array($this->getUser()->getRole()->getRole(), ['ROLE_ADMIN', 'ROLE_CUSTOMER_SERVIC'])
            || $u->getRole()->getLevel() == 4
            || $this->getUser()->getRole()->getLevel() == 6
        ) {
            $notes = $this->getRepository('LocalsBest\CommonBundle\Entity\Note')
                ->findNotesForAdmin($u, 'LocalsBestUserBundle:Event', $event);
        } else {
            $notes = $this->getRepository('LocalsBest\CommonBundle\Entity\Note')
                ->findMyNotes($this->getUser(), 'LocalsBestUserBundle:Event', $event, $users);
        }

        $allowAccess = false;
        if (
            $event->getCreatedBy() === $this->getUser()
            || in_array($event->getCreatedBy(), $users)
            || in_array($this->getUser(), $event->getAssignedTo()->toArray())
        ) {
        } else {
            foreach ($event->getShares() as $share) {
                if ($share->getUser() === $this->getUser() || in_array($share->getUser(), $users)) {
                    $allowAccess = true;
                }
            }
            foreach ($event->getAssignedTo() as $assignedTo) {
                if ($assignedTo->getId() === $this->getUser()->getId() || in_array($assignedTo, $users)) {
                    $allowAccess = true;
                }
            }

            foreach ($event->getShares() as $share) {
                $sharedWithUsernames[] = $share->getUser()->getUsername();
            }
        }
        
//        if ($allowAccess) {
            return array(
                'file' => $event,
                'sharedwithusernames' => implode(',', $sharedWithUsernames) . ',',
                'notes' => $notes,
            );
//        }
        
        throw $this->createNotFoundException('Event not found');
    }

    /**
     * Marked event as closed
     *
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function markCompleteAction(Request $request, $slug)
    {
        // Get Event by slug
        $event = $this->findOr404('LocalsBestUserBundle:Event', ['slug' => $slug], 'Event not found');

        // Get business staff
        if($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $users = array();
            $businesses = $this->getUser()->getBusinesses();
           
           foreach($businesses as $business) {
               $user = $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(array('id' => $business->getOwner()->getid()));
               $users[] = $user;
           }
        } else {
            $users = $this->getStaffs();
        }
        
        $allowAccess = false;
        
        if (
            $event->getCreatedBy() === $this->getUser()
            || in_array($event->getCreatedBy(), $users)
            || in_array($this->getUser(), $event->getAssignedTo()->toArray())
        ) {
            $allowAccess = true;
        } else {
            foreach ($event->getAssignedTo() as $assignedTo) {
                if ($assignedTo === $this->getUser() || in_array($assignedTo, $users)) {
                    $allowAccess = true;
                }
            }
        }
        
        if (!$allowAccess) {
            throw $this->createNotFoundException('Event not found!');
        }
        
        $response = array(
            'code'      => 0,
            'message'   => 'Some error occured'
        );
        
        $statusComplete = $this->getRepository('LocalsBestCommonBundle:Status')->findOneBy(array(
            'status' => 'closed'
        ));
        
        if (!$statusComplete) {
            throw new Exception('Invalid Status change');
        }
        // Update event information
        $event->setStatus($statusComplete)->setCompleted(time());
        // Save update
        $this->getRepository('LocalsBestUserBundle:Event')->save($event);

        if ($request->isXmlHttpRequest()) {
            $response = array(
                'code' => 1,
                'message' => 'Status changed successfully'
            );
            return new Response(json_encode($response));
        }
        // Return JSON response
        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer);
    }

    /**
     * Add Note for Event
     *
     * @param Request $request
     * @param string $slug
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function addNoteAction(Request $request, $slug)
    {
        // Get Event Entity
        $file = $this->findOr404('LocalsBestUserBundle:Event', ['slug' => $slug], 'Invalid share event');
        // Create Note Form object
        $form = $this->createForm(NoteType::class);
        // if form was submit
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // Create Note Entity
                $note = new Note($form->get('note')->getData());
                // Set information for Note
                $note
                    ->setObjectType(ObjectTypeType::Event)
                    ->setObjectId($file->getId())
                    ->setCreatedBy($this->getUser());
                // Attach Note to Event
                $file->addNote($note);
                // Save Event
                $this->getRepository('LocalsBestUserBundle:Event')->save($file);
                // Redirect user
                return $this->redirectToRoute('event_view', array('slug' => $slug));
            }
        }
        // Return params to render view
        return [
            'form' => $form->createView(),
            'slug' => $slug
        ];
    }

    /**
     * Remove Event
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse|Response
     */
    public function removeEventAction(Request $request, $id)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        if (!$this->get('localsbest.checker')->forAddon('events & alerts', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Get Event Entity
        $event = $this->findOr404('LocalsBestUserBundle:Event', array('id' => $id), 'Invalid object');

        // Detach Listing File from Event
        if($event->getListing()) {
            $event->setListing(null);
            $em->persist($event);
        }

        // Detach Closing File from Event
        if($event->getClosing()) {
            $event->setClosing(null);
            $em->persist($event);
        }
        // Remove Event
        $em->remove($event);
        // Update DB
        $em->flush();

        if($request->isXmlHttpRequest()) {
            $response = array(
                'code' => 1,
                'message' => 'successfully removed event'
            );

            return new Response(json_encode($response));
        } else {
            $referrer = $request->headers->get('referer');
            return $this->redirect($referrer);
        }
    }

    /**
     * Action to share event with users
     *
     * @param Request $request
     * @param string $slug
     *
     * @return RedirectResponse|Response
     */
    public function shareAction(Request $request, $slug)
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }
        $em = $this->getDoctrine()->getManager();

        // Get Event Entity
        $file = $this->findOr404('LocalsBestUserBundle:Event', array('slug' => $slug), 'Invalid share document');

        // if form was submit
        if ($request->getMethod() === 'POST') {
            $postData = $request->request->all();
            $shareUsernames = $postData['shares'];
            $shareUsernames = explode(', ', $shareUsernames);
            $shareUsernames = array_unique($shareUsernames);
            $sharedWithUsers = [];
            // Get users that already have his event as shared
            foreach ($file->getShares() as $share) {
                $sharedWithUsers[] = $share->getUser();
            }

            // Share Process
            foreach ($shareUsernames as $username) {
                if (!empty($username)) {
                    $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                        ->findOneBy(['username' => $username]);
                    if (!$user || !$user instanceof User || in_array($user, $sharedWithUsers)) {
                        continue;
                    }
                    // Create Share Entity
                    $share = new Share();
                    $share->setUser($user);
                    $time = time();
                    $randNum = rand();
                    $token = base64_encode($time . ':' . $randNum);
                    $share->setToken($token);
                    $share->setCreatedBy($this->getUser());
                    $share
                        ->setObjectType(ObjectTypeType::Event)
                        ->setObjectId($file->getId());
                    // Attache share to Event
                    $file->addShare($share);
                    $sharedWithUsers[] = $user;

                    if ($user->getPreference()->getMail() === TRUE) {
                        // Send Email about this
                        $this->getMailMan()->sendSharedMail($share);
                    }

                    if ($user->getPreference()->getSms() === TRUE) {
                        // Send SMS about this
                        $phone = $user->getPrimaryPhone()->getNumber();
                        $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                        $sender = $this->get('jhg_nexmo_sms');
                        $message = 'You have new shared event.';
                        $sender->sendText('+1' . $number, $message, null);
                    }
                }
            }
            // Save Event changes
            $this->getRepository('LocalsBestUserBundle:Event')->save($file);

            foreach ($file->getShares() as $share) {
                // Send Notification
                $this->get('localsbest.notification')
                    ->addNotification(
                        'New Event shared with you',
                        'event_share_response',
                        ['token' => $share->getToken()],
                        $sharedWithUsers,
                        [$share->getUser()]
                    )
                ;
            }
            // Redirect user
            return $this->redirectToRoute('event_index');
        }
        // Render view
        return $this->render('@LocalsBestUser/event/view.html.twig', array('file' => $file,));
    }

    /**
     * Redirect to Event View Page using Share token
     *
     * @param Request $request
     * @param string $token
     *
     * @return RedirectResponse
     */
    public function sharedViewAction(Request $request, $token)
    {
        if ($token) {
            $em = $this->getDoctrine()->getManager();

            // Get Share Entity by token
            $share = $em->getRepository('LocalsBest\UserBundle\Entity\Share')->findOneBy(array('token' => $token));

            if (!$share) {
                throw $this->createNotFoundException('Invalid or Expired token');
            }

            // Get Event Entity
            $eventExists = $em->getRepository('LocalsBest\UserBundle\Entity\Event')->find($share->getObjectId());

            if (!$eventExists) {
                throw $this->createNotFoundException('No such event found');
            }

            $response = (int) $request->get('response', null);

            if ($response === Share::STATUS_ACCEPT) {
                $share->setToken(NULL);
            }
            // Update share information
            $share->setStatus($response);
            $share->setUpdated(time());
            // Update DB
            $em->flush();
            // Redirect for user
            return $this->redirectToRoute('event_view', array('slug' => $eventExists->getSlug()));
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * Change Event Status
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function statusChangeAjaxAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get status from Request
        $statusId = $request->get('status');
        // Get Event
        $event = $this->findOr404('LocalsBestUserBundle:Event', ['id' => $id], 'Invalid item');
        // Get Status
        $status = $em->getRepository('LocalsBest\CommonBundle\Entity\Status')->find($statusId);
        
        if ($event) {
            // Set new Status for Event
            $event->setStatus($status);
            // Save changes
            $em->getRepository('LocalsBestUserBundle:Event')->save($event);
            // Update DB
            $em->flush();
        }
        // Redirect for user
        return $this->redirectToRoute('event_view', array('slug' => $event->getSlug()));
    }

    /**
     * Get Event for Calendar on Dashboard Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxEventsAction(Request $request)
    {
        // check user auth for it
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied!');
        }
        // Get start and end dates from request
        $startDate = $request->get('start');
        $endDate = $request->get('end');

        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');
        $events = $em->getRepository('LocalsBest\UserBundle\Entity\Event')->findAjaxEvents($startDate, $endDate, $this->getUser());
        $result = [];

        $arrayOfStatuses = ['Sold_Paid', 'Sold_paid', 'Contract_fell_thru'];

        // Create array with correct format
        /** @var Event $item */
        foreach ($events as $item) {
            $object = '';
            $objectType = null;
            $url = null;
            if (!is_null($item->getUser())) {
                $object = !is_null($item->getUser())
                    ? ($item->getUser()->getFirstName() . ' ' . $item->getUser()->getLastName())
                    : '';
                $objectType = 'User';
                $url = $this->generateUrl('user_view', ['username' => $item->getUser()->getUsername()]);
            }

            $result[] = [
                'id'          => $item->getId(),
                'title'       => $item->getCreatedBy()->getInitials() . ' - ' . $item->getTitle(),
                'url'         => !is_null($url) ? $url : $this->generateUrl('event_view', ['slug' => $item->getSlug()]),
                'start'       => !is_null($item->getTime()) ? $item->getTime()->format("Y-m-d H:i:s") : '',
                'end'         => !is_null($item->getEndTime()) ? $item->getEndTime()->format("Y-m-d H:i:s") : '',
                'description' => $item->getDescription(),
                'full_title'  => $item->getCreatedBy()->getFullName() . ' - ' . $item->getTitle(),
                'object'      => $object,
                'objectType'  => $objectType,
                'alert'       => (count($item->getAlerts()) > 0) ? true : false,
                'color'       => $this->getColor($item->getType()),
            ];
        }
        // Create JSON response
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        // Return JSON response
        return $response;
    }

    /**
     * Update Event Start and End Dates using Calendar on Dashboard Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxEventUpdateAction(Request $request)
    {
        // Check Request Method
        if($request->getMethod() !== 'POST') {
           return $this->sendError('Wrong request method.');
        }
        // Get Event ID
        $eventId = $request->get('eventId', null);

        if(is_null($eventId)) {
            return $this->sendError('Event ID is not set.');
        }

        $em = $this->getDoctrine()->getManager();

        // Get Event by ID
        $event = $em->getRepository('LocalsBestUserBundle:Event')->find($eventId);

        // Create start and end dates for Event
        $startTime = new DateTime($request->get('start'));
        $endTime = new DateTime($request->get('end'));
        // Set new Dates
        $event->setTime($startTime);
        $event->setEndTime($endTime);
        // update DB
        $em->flush();

        // Create Response
        $response = new Response(json_encode(array('status'=>'success')));
        $response->headers->set('Content-Type', 'application/json');
        // Send Response
        return $response;
    }

    /**
     * Send Error as JSON Response
     *
     * @param string $message
     *
     * @return Response
     */
    protected function sendError($message)
    {
        // Create Response
        $response = new Response(json_encode(['error' => $message]));
        $response->headers->set('Content-Type', 'application/json');
        // Send Response
        return $response;
    }

    /**
     * Get Color for Event Type
     *
     * @param string $type
     *
     * @return string
     */
    protected function getColor($type)
    {
        $color = '';
        switch ($type) {
            case 'Closing':
                $color = '#ED1317';
                break;
            case 'Listing':
                $color = '#B044E5';
                break;
            case 'Task':
                $color = '#000';
                break;
        }
        return $color;
    }
}
