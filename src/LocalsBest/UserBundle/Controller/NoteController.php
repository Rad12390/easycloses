<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\NoteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NoteController extends Controller
{
    /**
     * List all Notes
     *
     * @return array
     * @Template
     */
    public function indexAction()
    {
        // Get business staff
        if($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $em = $this->getDoctrine()->getManager();

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
        // Get Notes for current User
        $notes = $this->getRepository('LocalsBest\CommonBundle\Entity\Note')->findMyObjects($this->getUser(), $users);
        // Render view with params
        return array(
            'notes' => $notes,
            'users' => $users
        );
    }

    /**
     * Convert Contact object to Array
     *
     * @param object $contact
     * @param string $role
     *
     * @return array
     */
    protected function convertContactToArray($contact = null, $role = null)
    {
        $result = [];
        // Check Contact information
        if (is_null($contact) or is_null($contact->getEmail())) {
            return $result;
        }

        $result['email'] = $contact->getEmail();
        $result['role'] = $role ?: $contact->getRole();
        $result['contactName'] = $contact->getContactName();
        // Return Array
        return $result;
    }

    /**
     * Create and Attach Note to object
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function addNoteAction(Request $request, $id, $type)
    {
        $em = $this->getDoctrine()->getManager();
        
        $userNames = array();
        $sharedWithUsers = array();
        $emailCheckingObjectType = array();
        $curUser = $this->getUser();

        // Get object according to $type
        if($type === ObjectTypeType::Listing) {
            $file = $this->findOr404('LocalsBestUserBundle:Transaction', array('id' => $id), 'Invalid item');
        } else {
            $file = $this->findOr404($type, array('id' => $id), 'Invalid item');
        }

        // Create New Note
        $note = new Note();
        // Get List of Object Shared with by $type
        if (in_array(
                $type,
                [ObjectTypeType::DocumentUser, ObjectTypeType::DocumentSingle, ObjectTypeType::DocumentJob]
            )
        ) {
            $emailCheckingObjectType[] = $type;
            foreach ($file->getShares() as $share) {
                $array['email'] = $share->getUser()->getPrimaryEmail()->getEmail();
                $array['contactName'] = $share->getUser()->getFirstname() . ' ' . $share->getUser()->getFirstname();
                $array['role'] = $share->getUser()->getRole()->getName();
                $userNames[] = $array;
            }
        } elseif($type === ObjectTypeType::Document) {
            $emailCheckingObjectType[] = ObjectTypeType::Document;
            $creator = $file->getCreatedBy();
            $array['email'] = $creator->getPrimaryEmail()->getEmail();
            $array['contactName'] = $creator->getFirstname() . ' ' . $creator->getLastname();
            $array['role'] = $creator->getRole()->getName();
            $userNames[] = $array;
        } elseif ($type === ObjectTypeType::Transaction) {
            $emailCheckingObjectType[] = ObjectTypeType::Transaction;
            /** @var Listing $listing */
            $listing = $this->find('LocalsBestUserBundle:Listing', ['id' => $file->getListing()]);
            /** @var Closing $closing */
            $closing = $this->find('LocalsBestUserBundle:Closing', ['id' => $file->getClosing()]);
            
            if($listing != null) {
                if($listing->getSellerContact() !== null && $listing->getSellerContact()->getEmail() != null) {
                    $userNames[] = $this->convertContactToArray(
                        $listing->getSellerContact(),
                        $listing->getType() == 'Lease' ? 'Landlord' : 'Seller'
                    );
                }
                if(
                    $listing->getListingAgentContact() !== null
                    && $listing->getListingAgentContact()->getEmail() != null
                    && $listing->getListingAgentContact()->getEmail() !== $curUser->getPrimaryEmail()->getEmail()
                ) {
                    $userNames[] = $this->convertContactToArray($listing->getListingAgentContact(), 'Listing Agent');
                }

                if(
                    $listing->getListingOfficeContact() !== null
                    && $listing->getListingOfficeContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($listing->getListingOfficeContact(), 'Listing Office');
                }

                if(
                    $listing->getTitleCompanyContact() !== null
                    && $listing->getTitleCompanyContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($listing->getTitleCompanyContact(), 'Title Company');
                }
            }

            if($closing != null) {
                if($closing->getBuyerContact() !== null && $closing->getBuyerContact()->getEmail() != null) {
                    $userNames[] = $this->convertContactToArray(
                        $closing->getBuyerContact(),
                        $closing->getType() == 'Lease' ? 'Tenant' : 'Buyer'
                    );
                }

                if(
                    $closing->getBuyersAgentContact() !== null
                    && $closing->getBuyersAgentContact()->getEmail() != null
                    && $closing->getBuyersAgentContact()->getEmail() !== $curUser->getPrimaryEmail()->getEmail()
                ) {
                    $userNames[] = $this->convertContactToArray(
                        $closing->getBuyersAgentContact(),
                        $closing->getType() == 'Lease' ? 'Tenant Agent' : 'Buyer Agent'
                    );
                }

                if(
                    $closing->getBuyersOfficeContact() !== null
                    && $closing->getBuyersOfficeContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray(
                        $closing->getBuyersOfficeContact(),
                        $closing->getType() == 'Lease' ? 'Tenant Office' : 'Buyer Office'
                    );
                }

                if(
                    $closing->getEscrowCompanyContact() !== null
                    && $closing->getEscrowCompanyContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($closing->getEscrowCompanyContact(), 'Escrow Company');
                }

                if($closing->getLenderContact() !== null && $closing->getLenderContact()->getEmail() != null) {
                    $userNames[] = $this->convertContactToArray($closing->getLenderContact(), 'Lender');
                }

                if(
                    $closing->getHomeInspectorContact() !== null
                    && $closing->getHomeInspectorContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($closing->getHomeInspectorContact(), 'Home Inspector');
                }

                if(
                    $closing->getHomeInsuranceContact() !== null
                    && $closing->getHomeInsuranceContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($closing->getHomeInsuranceContact(), 'Home Insurance');
                }
            }

            if($listing === null && $closing != null) {
                if($closing->getSellerContact() !== null && $closing->getSellerContact()->getEmail() != null) {
                    $userNames[] = $this->convertContactToArray(
                        $closing->getSellerContact(),
                        $closing->getType() == 'Lease' ? 'Landlord' : 'Seller'
                    );
                }

                if(
                    $closing->getListingAgentContact() !== null
                    && $closing->getListingAgentContact()->getEmail() != null
                    && $closing->getListingAgentContact()->getEmail() !== $curUser->getPrimaryEmail()->getEmail()
                ) {
                    $userNames[] = $this->convertContactToArray($closing->getListingAgentContact(), 'Listing Agent');
                }

                if(
                    $closing->getListingOfficeContact() !== null
                    && $closing->getListingOfficeContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($closing->getListingOfficeContact(), 'Listing Office');
                }

                if(
                    $closing->getTitleCompanyContact() !== null
                    && $closing->getTitleCompanyContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($closing->getTitleCompanyContact(), 'Title Company');
                }
            }

            foreach($file->getContacts() as $transContact) {
                $userNames[] = $this->convertContactToArray($transContact);
            }
        } elseif ($type === ObjectTypeType::Listing) {
            
            $emailCheckingObjectType[] = ObjectTypeType::Listing;
            $listing = $this->findOr404(
                'LocalsBestUserBundle:Listing',
                ['id' => $file->getListing() ],
                'Listing not found'
            );

            if($listing != null) {
                if($listing->getSellerContact() !== null && $listing->getSellerContact()->getEmail() != null) {
                    $userNames[] = $this->convertContactToArray($listing->getSellerContact(), 'Seller');
                }

                if(
                    $listing->getListingAgentContact() !== null
                    && $listing->getListingAgentContact()->getEmail() != null
                    && $listing->getListingAgentContact()->getEmail() !== $curUser->getPrimaryEmail()->getEmail()
                ) {
                    $userNames[] = $this->convertContactToArray($listing->getListingAgentContact(), 'Listing Agent');
                }
                if(
                    $listing->getListingOfficeContact() !== null
                    && $listing->getListingOfficeContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($listing->getListingOfficeContact(), 'Listing Office');
                }

                if(
                    $listing->getTitleCompanyContact() !== null
                    && $listing->getTitleCompanyContact()->getEmail() != null
                ) {
                    $userNames[] = $this->convertContactToArray($listing->getTitleCompanyContact(), 'Title Company');
                }
            }
        } elseif ($type === ObjectTypeType::Job) {
            
            $emailCheckingObjectType[] = ObjectTypeType::Job;
            foreach ($file->getContacts() as $contact) {
                if($contact->getEmail() != null) {
                    if($contact->getEmail() == $curUser->getPrimaryEmail()->getEmail()) {
                        continue;
                    }
                    $array['email'] = $contact->getEmail();
                    $array['contactName'] = $contact->getContactName();
                    $array['role'] = $contact->getRole();
                    $userNames[] = $array;
                }
            }
        } elseif ($type === ObjectTypeType::Event) {
            foreach ($file->getShares() as $share) {
                $array['email'] = $share->getUser()->getPrimaryEmail()->getEmail();
                $array['contactName'] = $share->getUser()->getFirstname() . ' ' . $share->getUser()->getLastname();
                $array['role'] = $share->getUser()->getRole()->getName();
                $userNames[] = $array;
            }
        } elseif ($type === ObjectTypeType::User) {
            if($file->getId() !== $curUser->getId()) {
                $emailCheckingObjectType[] = ObjectTypeType::User;
                $userNames[0]['email'] = $file->getPrimaryEmail()->getEmail();
                $userNames[0]['contactName'] = $file->getFirstName() . ' ' . $file->getLastName();
                $userNames[0]['role'] = $file->getRole()->getName();
            }
        } elseif ($type === ObjectTypeType::Contact) {
            if($file->getUser() !== $this->getUser()) {
                $userNames[0]['email'] = $file->getEmail();
                $userNames[0]['contactName'] = $file->getFirstName() . ' ' . $file->getLastName();
                $userNames[0]['role'] = 'Contact';
            }
        }

        $emails = [];
        $result = [];
        foreach ($userNames as $row) {
            if (isset($row['email'])) {
                array_push($result, $row);
                array_push($emails, $row['email']);
            }
        }
        $userNames = $result;

        // Set Note Status
        $note->setStatus($this->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus());
        // Create Note Form object
        $form = $this->createForm(NoteType::class);
        // if form was submit
       
        if ($request->getMethod() === 'POST') {
           
            $form->handleRequest($request);
            if ($form->isValid()) {
                if($type === ObjectTypeType::Event) {
                    // Set status for Event
                    $file->setStatus($this->getRepository('LocalsBestUserBundle:Event')->getActiveStatus());
                }
                $business = null;

                if($this->get('session')->get('current_business')) {
                    // Get current Business
                    $business = $this->getRepository('LocalsBestUserBundle:Business')
                        ->find($this->get('session')->get('current_business'));
                }

                $createdByUser = $this->getUnmaskingUser();

                $note   ->setNote($form->get('note')->getData())
                        ->setPrivate(true)
                        ->setImportant($form->get('important')->getData())
                        ->setObjectType($type)
                        ->setUser($createdByUser)
                        ->setCreatedBy($createdByUser);
                
                if ($business != null) {
                    $note->setOwner($business);
                }

                if ($file != null){
                    if ($type === ObjectTypeType::Listing) {
                        $listing = $file->getListing();
                        // Attach Note to object
                        $note->setObjectId($listing->getId());
                        $file->addShowingNote($note);
                        $note->setTransaction($file);
                        // Save Note
                        $em->persist($note);

                        if ($type === ObjectTypeType::Listing) {
                            $em->getRepository('LocalsBestUserBundle:Transaction')
                                ->save($file);
                        } else {
                            $em->getRepository($type)->save($file);
                        }
                    } else {
                        $note->setObjectId($file->getId());
                    }
                    // attach Note to object
                    $file->addNote($note);
                } else {
                    $note->setObjectId($id);
                    $file = $note;
                }

                // Get List of users IDs Note Shared with
                $userNames = $request->request->get('usernames', []);
                $noEmailUsers = [];

                if (in_array('transaction_party', $userNames)) {
                    unset($userNames[$key = array_search ('transaction_party', $userNames)]);
                    $docApprovers = $em->getRepository('LocalsBestUserBundle:User')->findActiveDocApprovers($business);

                    foreach ($docApprovers as $dApprove) {
                        $userNames[] = $dApprove['email'];
                        $noEmailUsers[] = $dApprove['email'];
                    }
                }

                if (count($userNames)) {
                    // Sharing process
                    foreach ($userNames as $name) {
                        if ($type !== ObjectTypeType::Contact) {
                            if (in_array($type, $emailCheckingObjectType)) {
                                $emailExists = $em->getRepository('LocalsBest\UserBundle\Entity\Email')->findOneByEmail($name);
                                if ($emailExists) {
                                    // Get User by Primary Email
                                    $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                                        ->findOneBy(['primaryEmail' => $emailExists]);

                                    if ($user === null) {
                                        // Get Contact by email
                                        $contact = $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')
                                            ->findOneBy(array('email' => $name));

                                        if($contact) {
                                            $this->getMailMan()->sendNoteMailToContact($contact, $note, $createdByUser, $file);
                                        }
                                        continue;
                                    }
                                } else {
                                    $user = null;
                                    if(filter_var($name, FILTER_VALIDATE_EMAIL)) {
                                        // Send Email about this
                                        $this->getMailMan()->sendMail(
                                            'New Transaction Note for "' . $file->getFullAddress() . '"',
                                            '@LocalsBestNotification/mails/share-note-contact.html.twig',
                                            array('contact' => null, 'note' => $note, 'user' => $createdByUser),
                                            array($name)
                                        );
                                    }
                                }
                            } else {
                                $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                                    ->findOneBy(['username' => $name]);
                            }

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
                            $share->setCreatedBy($createdByUser);
                            $share->setObjectId($file->getId());
                            $message = 'You got a new Note.';

                            // According to $type send Notification about this
                            if ($type === ObjectTypeType::Document) {
                                $share->setObjectType(ObjectTypeType::Document);
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'document_view',
                                        array('slug' => $file->getSlug()),
                                        array($user),
                                        array($createdByUser)
                                    )
                                ;
                            } elseif ($type === ObjectTypeType::Listing) {
                                $share->setObjectType(ObjectTypeType::Transaction);
                            } elseif ($type === ObjectTypeType::Transaction) {
                                $share->setObjectType(ObjectTypeType::Transaction);
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'transaction_view',
                                        array('id' => $file->getId()),
                                        array($user),
                                        array($createdByUser)
                                    )
                                ;
                            } elseif ($type === ObjectTypeType::Job) {
                                $share->setObjectType(ObjectTypeType::Job);
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'job_view',
                                        array('id' => $file->getId()),
                                        array($user),
                                        array($createdByUser)
                                    )
                                ;
                            } elseif ($type === ObjectTypeType::Event) {
                                $share->setObjectType(ObjectTypeType::Event);
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'event_view',
                                        array('slug' => $file->getSlug()),
                                        array($user),
                                        array($createdByUser)
                                    )
                                ;
                            } elseif ($type === ObjectTypeType::User) {
                                $share->setObjectType(ObjectTypeType::User);
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'user_view',
                                        array('username' => $file == $user ? $createdByUser->getUsername() : $file->getUsername()),
                                        array($user),
                                        array($createdByUser)
                                    )
                                ;
                            }
                            $file->addShare($share);
                            $note->addShare($share);

                            $sharedWithUsers[] = $user;

                            // don't send emails for all users
                            if (in_array($name, $noEmailUsers)) {
                                unset($noEmailUsers[ array_search($name, $noEmailUsers) ]);
                                continue;
                            }

                            if ($user->getPreference()->getMail() === TRUE) {
                                // Send Email about this
                                $this->getMailMan()->sendSharedMail($share, $note);
                            }

                            if ($user->getPreference()->getSms() === TRUE) {
                                // Send SMS about this
                                $message1= 'Hi ' . $user->getFullName().","."\n";
                                $message1.= "Here's the new note".":"."\n";
                                $message1.= $note->getNote();
                                $message1.= "<br><br>";
                                $message1.= $createdByUser->getFullName();
                                $message1.= $createdByUser->getPrimaryEmail()->getEmail();
                                $message1.= $createdByUser->getPrimaryPhone()->getNumber();
                                sleep(1);
                                $phone = $user->getPrimaryPhone()->getNumber();
                                $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                                $sender = $this->get('jhg_nexmo_sms');
                                $sender->sendText('+1' . $number, $message1, null);
                            }
                        } else {
                            if ($file->getAssignTo() !== $createdByUser) {
                                // Create Share Entity
                                $share = new Share();
                                $share->setUser($file->getAssignTo());
                                $share->setToken(base64_encode(time() . ':' . rand()));
                                $share->setCreatedBy($createdByUser);
                                $share->setObjectId($file->getId());
                                $share->setObjectType(ObjectTypeType::Contact);

                                $message = 'You got a new Note.';
                                // Send Notification about this
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'contact_view',
                                        array('id' => $file->getId()),
                                        array($file->getAssignTo()),
                                        array($createdByUser)
                                    )
                                ;
                                $file->addShare($share);
                                // Attache Share to Note
                                $note->addShare($share);

                                if ($file->getAssignTo()->getPreference()->getMail() === TRUE) {
                                    // Send Email about this
                                    $this->getMailMan()->sendSharedMail($share, $note);
                                }

                                if ($file->getAssignTo()->getPreference()->getSms() === TRUE) {
                                    // Send SMS about this
                                    $message1= 'Hi ' . $file->getAssignTo()->getFullName().","."\n";
                                    $message1.= "Here's the new note".":"."\n";
                                    $message1.= $note->getNote();
                                    $message1.= "<br><br>";
                                    $message1.= $createdByUser->getFullName();
                                    $message1.= $createdByUser->getPrimaryEmail()->getEmail();
                                    $message1.= $createdByUser->getPrimaryPhone()->getNumber();
                                    sleep(1);
                                    $phone = $file->getAssignTo()->getPrimaryPhone()->getNumber();
                                    $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                                    $sender = $this->get('jhg_nexmo_sms');
                                    $sender->sendText('+1' . $number, $message1, null);
                                }
                            }

                            if (
                                $createdByUser !== $file->getGeneratedBy()
                                && $file->getAssignTo() !== $file->getGeneratedBy()
                            ) {
                                $share = new Share();
                                $share->setUser($file->getGeneratedBy());
                                $share->setToken($share->setToken(base64_encode(time() . ':' . rand())));
                                $share->setCreatedBy($createdByUser);
                                $share->setObjectId($file->getId());
                                $share->setObjectType(ObjectTypeType::Contact);

                                $message = 'You got a new Note.';
                                // Send Notification about this
                                $this->get('localsbest.notification')
                                    ->addNotification(
                                        $message,
                                        'contact_view',
                                        array('id' => $file->getId()),
                                        array($file->getGeneratedBy()),
                                        array($createdByUser)
                                    )
                                ;

                                $file->addShare($share);
                                $note->addShare($share);

                                if ($file->getGeneratedBy()->getPreference()->getMail() === TRUE) {
                                    // Send Email about this
                                    $this->getMailMan()->sendSharedMail($share, $note);
                                }

                                if ($file->getGeneratedBy()->getPreference()->getSms() === TRUE) {
                                    // Send SMS about this
                                    $message1= 'Hi ' . $file->getGeneratedBy()->getFullName().","."\n";
                                    $message1.= "Here's the new note".":"."\n";
                                    $message1.= $note->getNote();
                                    $message1.= "<br><br>";
                                    $message1.= $createdByUser->getFullName();
                                    $message1.= $createdByUser->getPrimaryEmail()->getEmail();
                                    $message1.= $createdByUser->getPrimaryPhone()->getNumber();
                                    $phone = $file->getGeneratedBy()->getPrimaryPhone()->getNumber();
                                    $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                                    $sender = $this->get('jhg_nexmo_sms');
                                    $sender->sendText('+1' . $number, $message1, null);
                                }
                            }

                            $contact = $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')
                                ->findOneBy(['email' => $name]);
                            if($contact) {
                                $this->getMailMan()->sendNoteMailToContact($contact, $note, $createdByUser);
                            }
                            continue;
                        }
                    }
                }

                $createPost = $request->request->get('create_post', null);

                if ($createPost !== null) {
                    $title = $request->request->get('post_title', 'Default Title');
                    $body = $note->getNote();

                    $images = $request->files->get('files');

                    $this->get('localsbest.wp_actions')->sendPost($this->getUser(), $title, $body, $images);
                }

                // Save changes
                $em->persist($note);
                $em->persist($file);
                $em->flush();

                if ($type === ObjectTypeType::Document) {
                    foreach ($file->getShares() as $share) {
                        // Send Notification about this
                        $this->get('localsbest.notification')
                            ->addNotification(
                                'New Document shared with you',
                                'document_share_response',
                                ['token' => $share->getToken()],
                                $sharedWithUsers,
                                [$share->getUser()]
                            )
                        ;
                    }
                }
                // Return to prev page
                return $this->redirect($request->server->get('HTTP_REFERER'));
            }
            else {
                die('errors');
            }
        }
          
        $staff = $em->getRepository('LocalsBestUserBundle:User')
            ->findFullStaffs($this->getUser(), $this->getBusiness());

        // $secondStaff - staff of members business that current user checked
        // by default $secondStaff is empty
        $secondStaff = null;
        // if current user is admin or customer service and check member detail page
        if(
            in_array($this->getUser()->getRole()->getLevel(), [1, 2])
            && $type == 'LocalsBestUserBundle:User'
        ) {
            // get member business
            $secondBusiness = $file->getBusinesses()->first();
            // get managers staff
            $secondStaff = $em->getRepository('LocalsBestUserBundle:User')->findManagerStaff($secondBusiness);
        }
        
        // Render view with params
//        return ['form' => $form->createView(),
//            'isTransaction' => $type == 'LocalsBestUserBundle:Transaction' ? true : false,
//            'id' => $id,
//            'object' => $file,
//            'objectType'=> $type,
//            'userNames' => $userNames,
//            'secondStaff' => $secondStaff,
//            'staff' => $staff,
//        ];
        return $this->render('@LocalsBestUser/note/addNote.html.twig', array('form' => $form->createView(),
            'isTransaction' => $type == 'LocalsBestUserBundle:Transaction' ? true : false,
            'id' => $id,
            'object' => $file,
            'objectType'=> $type,
            'userNames' => $userNames,
            'secondStaff' => $secondStaff,
            'staff' => $staff)
        );
    }

    /**
     * Edit Note
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function editNoteAction(Request $request, $id)
    {
        $userNames = [];
        
        if($id) {
            // Get Note by ID
            $note = $this->findOr404('LocalsBestCommonBundle:Note', ['id' => $id]);

            foreach ($note->getShares() as $share) {
                // Get Users Note Shared with
                $userNames[] = $share->getUser()->getPrimaryEmail()->getEmail();
            }
        } else {
            // Create new Note
            $note = new Note();
            $note
                ->setStatus($this->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus())
                ->setPrivate(true);
        }
        // Create Note Form object
        $form = $this->createForm(NoteType::class, $note);
        // If form was submit
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            // Update Note information
            $note
                ->setNote($form->get('note')->getData())
                ->setPrivate(true)
                ->setImportant($form->get('important')->getData());
            $this->getDoctrine()->getManager()->flush();

            // Redirect user to prev page
            $referrer = $request->headers->get('referer');
            return new RedirectResponse($referrer);
        }
        // Render view with params
        return array('form' => $form->createView(),
            'id' => $id,
            'userNames' => $userNames
        );
    }

    /**
     * Set Note to Important
     *
     * @param int $id
     * @param int $objectId
     * @param string $objectType
     *
     * @return Response
     */
    public function reviewImportantAction($id, $objectId, $objectType)
    {
        $response = array(
            'code'      => 0,
            'message'   => 'Some error occured'
        );

        // Get Note Entity by ID
        $note = $this->findOr404('LocalsBestCommonBundle:Note', ['id' => $id]);
        // Get Object Entity by ID
        $object = $this->findOr404($objectType, array('id' => $objectId));
        
        if (!$note) {
            throw $this->createNotFoundException('No such Note found');
        }

        if (!$object) {
            throw $this->createNotFoundException('No such object found');
        }
        // Get business staff
        $users = $this->getStaffs();

        // Check permissions for current user
        if (
            $object->getCreatedBy() === $this->getUser()
            || in_array($object->getCreatedBy(), $users)
            || (
                !is_null($object->getVendor())
                && $object->getVendor()->getId() === $this->getUser()->getId()
            )
        ) {
            // Set Note important
            $note
                ->setImportant(true)
                ->setUpdated(time());
            // Update Note
            $this->getDoctrine()->getManager()->getRepository('LocalsBestCommonBundle:Note')->save($note);
            
            $response = array(
                'code'      => 1,
                'message'   => 'Note has been set important successfully'
            );
        }
        // Send JSON response
        return new Response(json_encode($response));
    }

    /**
     * Set Note to NOT Important
     *
     * @param int $id
     * @param int $objectId
     * @param string $objectType
     * @return Response
     */
    public function reviewNotImportantAction($id, $objectId, $objectType)
    {
        $response = array(
            'code'      => 0,
            'message'   => 'Some error occured'
        );
        // Get Note Entity by ID
        $note = $this->findOr404('LocalsBestCommonBundle:Note', array('id' => $id));
        // Get Object Entity by ID
        $object = $this->findOr404($objectType, array('id' => $objectId));
        
        if (!$note) {
            throw $this->createNotFoundException('No such Note found');
        }
        
        if (!$object) {
            throw $this->createNotFoundException('No such object found');
        }
        // Get business staff
        $users = $this->getStaffs();
        // Check user permissions
        if (
            $object->getCreatedBy() === $this->getUser()
            || in_array($object->getCreatedBy(), $users)
            || (
                !is_null($object->getVendor())
                && $object->getVendor()->getId() === $this->getUser()->getId()
            )
        ) {
            // Set Note NOT Important
            $note->setImportant(false);
            // Save changes
            $this->getDoctrine()->getManager()->getRepository('LocalsBestCommonBundle:Note')->save($note);
            
            $response = array(
                'code'      => 1,
                'message'   => 'Note has been set not important successfully'
            );
        }
        // Return JSON response
        return new Response(json_encode($response));
    }

    /**
     * Display Note for Dashboard Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function apiListAction(Request $request)
    {
        // Limit for Notes
        $notesPerPage = 10;
        $page = $request->get('page', 1);
        $type = $request->get('type', 'all');

        $em = $this->getDoctrine()->getManager();
        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');
        // Get Notes for Display
        $notes = $em->getRepository('LocalsBestCommonBundle:Note')
            ->findNotesForMainPage($this->getUser(), $type, ($page-1) * $notesPerPage, $notesPerPage);
        $allNotes = $em->getRepository('LocalsBestCommonBundle:Note')
            ->findNotesForMainPage($this->getUser(), $type);

        // empty answer
        $e = '<li class="in"><div style="text-align: center"><span class="name">There are no notes.</span></div></li>';

        if (count($allNotes) == 0) {
            // Return JSON response
            return new Response(json_encode([
                'html' => $e,
                'more10' => false,
            ]));
        }
        // Return JSON response
        return new Response(json_encode([
            'html' => $this->renderView('@LocalsBestUser/note/apiList.html.twig', ['notes' => $notes]),
            'more10' => count($allNotes) > ($page*$notesPerPage) ? true : false,
        ]));
    }

    /**
     * Hide Note for Shared with Users
     *
     * @param Request $request
     *
     * @return Response
     */
    public function apiHideNoteAction(Request $request)
    {
        // Get ID from Request
        $id = $request->get('id', null);

        $em = $this->getDoctrine()->getManager();
        // Get Note by ID
        $note = $em->getRepository('LocalsBestCommonBundle:Note')->find($id);
        $shares = $note->getShares();
        // Get all Shares for this Note
        foreach($shares as $share) {
            if($share->getUser() == $this->getUser()){
                // Set Share visible option to false
                $share->setIsVisible(false);
            }
        }
        // Save changes
        $em->flush();

        // Return JSON response
        return new Response(json_encode([
            'result' => 'success',
        ]));
    }
}
