<?php

namespace LocalsBest\UserBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\InviteStatusType;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\ClientBusiness;
use LocalsBest\UserBundle\Entity\Email;
use LocalsBest\UserBundle\Entity\PaidInvite;
use LocalsBest\UserBundle\Entity\Phone;
use LocalsBest\UserBundle\Entity\Role;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\ForgotType;
use LocalsBest\UserBundle\Form\NewRegisterType;
use LocalsBest\UserBundle\Form\RegisterType;
use LocalsBest\UserBundle\Form\ResetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class AuthController extends Controller
{

    /**
     * Show Login Page
     *
     * @param Request $request
     * @param string $slug
     * @return array|RedirectResponse|Response
     * @Template
     */
    public function loginAction(Request $request, $slug =null)
    {
        $business = null;
        $em = $this->getDoctrine()->getManager();

        // If slug is not empty look for Business Entity using slug
        if (!is_null($slug)) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $slug]);
        }

        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/msie ([2-9]|10)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return $this->render('@LocalsBestUser/auth/ie_message.html.twig');
        }

        // If user already login redirect him to
        if ($this->getUser()) {
            if ($this->getUser()->getRole()->getRole() === Role::ROLE_VENDOR) {
                // Job Summary Page
               return $this->redirect($this->generateUrl('job_index'));
            }
            // Home Page
            return $this->redirect($this->generateUrl('locals_best_user_homepage'));
        }

        $error = '';
        $session = $request->getSession();

        // Create Forgot Password Form object
        $forgotPasswordForm = $this->createForm(ForgotType::class);

        // Get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        if (isset($error) && is_object($error) && method_exists($error, 'getMessage')) {
            $error = $error->getMessage();
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        if ($lastUsername !== '') {
            // Look for user using entered username
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $lastUsername]);

            // If User Entity exists and belong to CRRLI show flash messages
            if ($user !== null && count($user->getBusinesses())>0 && $user->getBusinesses()[0]->getId() == 50) {
                $this->addFlash(
                    'success',
                    'If this is your 1st time logging in please enter your regular user name and use the following 
                    password: tomorrow'
                );
                $this->addFlash(
                    'success',
                    'If you logged in before and forgot your new password please click the "no worries, click here 
                    to reset your password." link below'
                );
            }
        }

        return array(
            // last username entered by the user
            'last_email'    => $lastUsername,
            'error'         => $error,
            'forgot_form'   => $forgotPasswordForm->createView(),
            'slug'          => $slug,
            'business'      => $business,
        );
    }

    /**
     * Show Register Page
     *
     * @param Request $request
     * @param null $token
     * @return array|RedirectResponse|Response
     * @Template
     */
    public function registerAction(Request $request, $token = null)
    {
        // If $token is empty redirect user to Login Page
        if (is_null($token)){
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->getDoctrine()->getManager();

        if( isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/msie ([2-9]|10)/i', $_SERVER['HTTP_USER_AGENT']) ) {
            return $this->render('@LocalsBestUser/auth/ie_message.html.twig');
        }

        $invited = false;
        // Create empty User Entity
        /** @var User $user */
        $user = new User();
        $user->getContact()->addEmail(new Email());
        $user->getContact()->addPhone(new Phone());

        // If $token is not empty
        if ($token) {
            // Look for PaidInvite Entity using $token
            /** @var PaidInvite $paidInvite */
            $paidInvite = $em->getRepository('LocalsBest\UserBundle\Entity\PaidInvite')
                ->findOneBy(array('token' => $token));

            // If PaidInvite exists and don't have Payment entity attached redirect user to join page
            if ($paidInvite !== null && $paidInvite->getPayment() === null) {
                return $this->redirectToRoute('join', ['token' => $token]);
            }

            // If PaidInvite exists
            if (null !== $paidInvite) {
                // Look for Manager Role Entity
                $role = $em->getRepository('LocalsBest\UserBundle\Entity\Role')->find(4);
                $email = $paidInvite->getRecipient();
                // Look for Email Entity using PaidInvite email
                $emailExists = $em->getRepository('LocalsBest\UserBundle\Entity\Email')->findOneByEmail($email);

                // If Email Entity exists
                if ($emailExists) {
                    // Look for User Entity by primary email using Email Entity
                    $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(
                        ['primaryEmail' => $emailExists]
                    );

                    // Take User Entity who send paid invite
                    $createdBy = $paidInvite->getCreatedBy();

                    // If $createBy contains $user in his vendor list
                    if ($createdBy->getMyVendors()->contains($user)) {
                        $this->addFlash(
                            'warning',
                            'You already have a connection with this agent. Please proceed to login page.'
                        );
                        // redirect $user to Login Page
                        return $this->redirectToRoute('login');
                    } else {
                        // Add $user to $createdBy vendors list
                        $createdBy->setMyVendors($user);
                        $user->setVendorsWithMe($createdBy);
                        $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($createdBy);
                    }

                    // If $user don't have parent User Entity
                    if ($user->getCreatedBy() === null) {
                        // Set $createdBy as $user parent User Entity
                        $user->setCreatedBy($createdBy);
                    }

                    // Attach $user to PaidInvite
                    $paidInvite->setUser($user);
                    $em->flush();

                    if (!in_array($createdBy->getRole()->getRole(), array('ROLE_ADMIN', 'ROLE_CUSTOMER_SERVIC'))) {
                        // Create new Contact Entity using $createdBy information
                        $contact = new AllContact();

                        $contact
                            ->setInvitation(true)
                            ->setFirstName($createdBy->getFirstName())
                            ->setLastName($createdBy->getLastName())
                            ->setNumber($createdBy->getPrimaryPhone()->getNumber())
                            ->setEmail($createdBy->getPrimaryEmail()->getEmail())
                            ->setUser($createdBy)
                            ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                            ->setCreatedBy($user)
                        ;
                        $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);

                        // Attach new Contact entity to $user
                        $user->addAllContact($contact);
                    }
                    $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);

                    // send Welcome Email
                    $this->getMailMan()->sendWelcomeMail($user);
                    // Show flash message
                    $this->addSuccessMessage('Account updated successfully.Proceed to login');
                    // redirect to Login Page
                    return $this->redirect($this->generateUrl('login'));
                } else {
                    // Set information for $user User Entity
                    $user->setRole($role);
                    $user->getContact()->getEmails()->first()->setEmail($email);
                    $user->setUsername($email);
                    $user->setCreatedBy($paidInvite->getCreatedBy());

                    $decodeToken = base64_decode($paidInvite->getPayment()->getToken());
                    $filterToken = explode(':', $decodeToken);
                    $data = json_decode($filterToken[1] . ':' . $filterToken[2] . ":" . $filterToken[3]);
                    $user->setVendorCategory($data->category);
                }
            } else {
                // Look for Free Invite Entity using token
                $invite = $em->getRepository('LocalsBest\UserBundle\Entity\Invite')
                    ->findOneBy(array('token' => $token));

                if (!$invite) {
                    throw $this->createNotFoundException('Invalid or Expired token');
                }
                /** @var User $createdBy */
                $createdBy = $this->findOr404('LocalsBestUserBundle:User', array(
                    'id' => $invite->getCreatedBy()->getId()
                ), 'User not found');

                $email = $invite->getEmail();
                // Look for Email Entity using Free Invite email
                $emailExists = $em->getRepository('LocalsBest\UserBundle\Entity\Email')->findOneByEmail($email);

                // Look for User Entity by primary email using Email Entity
                $userByEmail = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(
                    ['primaryEmail' => $emailExists]
                );

                if ($emailExists && $userByEmail !== null) {
                    $user = $userByEmail;
                    // if Free Invite for Vendor
                    if ($invite->getRole()->getRole() === "ROLE_VENDOR") {
                        $role = $em->getRepository('LocalsBest\UserBundle\Entity\Role')->find(4);

                        // If $createBy contains $user in his vendor list
                        if ($createdBy->getMyVendors()->contains($user)) {
                            // Show flash message
                            $this->addFlash(
                                'warning',
                                'You already have a connection with this agent. Please proceed to login page.'
                            );
                            // Redirect user to Login Page
                            return $this->redirectToRoute('login');
                        } else {
                            // Add $user to $createdBy vendors list
                            $createdBy->setMyVendors($user);
                            $user->setVendorsWithMe($createdBy);
                            $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($createdBy);
                        }
                    } else {
                        // if Free Invite is NOT for Vendor
                        $role = $invite->getRole();

                        /**
                         * A little bit Magic
                         * Check if businesses $createdBy already contains $user
                         * if Yes then show flash message and redirect user to Login Page
                         * if No add $user to $createdBy business
                         */
                        if (count($createdBy->getOwnedBusiness()) > 0) {
                            /** @var Business $business */
                            foreach ($createdBy->getOwnedBusiness() as $business) {

                                if ($business->getStaffs()->contains($user)) {
                                    $this->addFlash(
                                        'warning',
                                        'You already have an account with this company. Please proceed to login page.'
                                    );
                                    return $this->redirectToRoute('login');
                                } else {
                                    $user->addBusiness($business);
                                    $business->addStaff($user);
                                }
                            }
                        } else {
                            if (count($createdBy->getBusinesses()) > 0) {
                                foreach ($createdBy->getBusinesses() as $business) {
                                    if ($business->getStaffs()->contains($user)) {
                                        $this->addFlash(
                                            'warning',
                                            'You already have an account with this company. Please proceed to login page.'
                                        );
                                        return $this->redirectToRoute('login');
                                    } else {
                                        $user->addBusiness($business);
                                        $business->addStaff($user);
                                    }
                                }
                            }
                        }
                        /**
                         * End of Magic
                         */
                    }

                    $user->setRole($role);
                    // If $user don't have parent User Entity
                    if($user->getCreatedBy() === null) {
                        // Set $createdBy as $user parent User Entity
                        $user->setCreatedBy($createdBy);
                    }

//                    $em->persist($business);
                    // Update Free Invite Information
                    $invite->setToken(null);
                    $invite->setStatus(InviteStatusType::ACCEPTED);

                    $em->persist($invite);
                    $em->flush();

                    if (!in_array($createdBy->getRole()->getRole(), array('ROLE_ADMIN', 'ROLE_CUSTOMER_SERVIC'))) {
                        // Create new Contact Entity using $createdBy information
                        $contact = new AllContact();

                        $contact
                            ->setInvitation(true)
                            ->setFirstName($createdBy->getFirstName())
                            ->setLastName($createdBy->getLastName())
                            ->setNumber($createdBy->getPrimaryPhone()->getNumber())
                            ->setEmail($createdBy->getPrimaryEmail()->getEmail())
                            ->setUser($createdBy)
                            ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                            ->setCreatedBy($user)
                        ;
                        $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);

                        // attach Contact Entity to $user
                        $user->addAllContact($contact);
                    }

                    $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);

                    // Send Welcome Email to $user
                    $this->getMailMan()->sendWelcomeMail($user);
                    // Show flash message
                    $this->addSuccessMessage('Account updated successfully. Proceed to login');
                    // Redirect to Login Page
                    return $this->redirect($this->generateUrl('login'));
                }

                $decodeToken = base64_decode($invite->getToken());
                $filterToken = explode(':', $decodeToken);

                $inviteTime = $filterToken[0];
                if ($invite->getRole()->getRole() === "ROLE_VENDOR") {
                    $data = json_decode($filterToken[1] . ':' . $filterToken[2] . ":" . $filterToken[3]);
                }

                $tokenExpiry = $this->container->getParameter('token_expiry');
                $validRegistrationTime = (time() - $tokenExpiry);

                if (false && $inviteTime > $validRegistrationTime) {
                    $invite->setStatus(InviteStatusType::EXPIRED);
                    $em->flush();
                    throw $this->createNotFoundException('Invalid or Expired token');
                }

                $invited = true;
                $email = $invite->getEmail();

                if ($invite->getRole()->getRole() === "ROLE_VENDOR") {
                    $role = $em->getRepository('LocalsBest\UserBundle\Entity\Role')->find(4);
                    $user->setVendorCategory($data->category);

                } else {
                    $role = $invite->getRole();
                }

                $user->getContact()->getEmails()->first()->setEmail($email);
                $user->setRole($role);
                $user->setUsername($email);
            }
        }

        // Create Register Form object
        $form = $this->createForm(RegisterType::class, $user);

        // If Register Form was submit
        if ($request->getMethod() === 'POST') {

            $em->getFilters()->disable('softdeleteable');

            // Put request information to Register Form
            $form->handleRequest($request);

            if ($form->isValid()) {
                $user = $form->getData();

                if ($token) {
                    if (isset($paidInvite)) {
                        // update user and paid invite information
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($user);
                        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                        $user->setPassword($password);
                        $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);

                        $inviteManager = $paidInvite->getCreatedBy()->getBusinesses()->first()->getOwner();
                        $inviteManager->setMyVendors($user);
                        $user->setVendorsWithMe($inviteManager);
                        $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($inviteManager);
                        $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);

                        $paidInvite->setUser($user);
                        $em->flush();

                        // Send Welcome Email
                        $this->getMailMan()->sendWelcomeMail($user);
                        // Show flash message
                        $this->addSuccessMessage('Account created successfully. Proceed to login');
                        // Redirect to Login Page
                        return $this->redirect($this->generateUrl('login'));
                    }

                    if (isset($invite)) {
                        // update user and free invite information
                        $user->getContact()->getEmails()->first()->setEmail($email);
                        $user->setCreatedBy($createdBy);
                        if ($invite->getRole()->getRole() !== 'ROLE_VENDOR') {
                            $invite->setToken(null);
                        }
                        $invite->setStatus(InviteStatusType::ACCEPTED);

                        if ($invite->getRole()->getRole() === 'ROLE_VENDOR') {
                            $factory = $this->get('security.encoder_factory');
                            $encoder = $factory->getEncoder($user);
                            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                            $user->setPassword($password);
                            $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                            // Send Welcome Email
                            $this->getMailMan()->sendWelcomeMail($user);
                            // Show flash message
                            $this->addSuccessMessage('Account created successfully. Proceed to login');

                            $createdBy->setMyVendors($user);
                            $user->setVendorsWithMe($createdBy);
                            $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($createdBy);
                            $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                            // Redirect to Login Page
                            return $this->redirect($this->generateUrl('login'));
                        }

                        /** @var AllContact $originContact */
                        $originContact = $invite->getContact();

                        if ($originContact !== null) {
                            $originContact->setUser($user);
                        }

                        $role = [
                            'ROLE_ADMIN',
                            'ROLE_CUSTOMER_SERVIC',
                            'ROLE_NETWORK_MANAGER',
                            'ROLE_MANAGER',
                            'ROLE_TECH_DIRECTOR'
                        ];

                        if (!in_array($createdBy->getRole()->getRole(), array('ROLE_ADMIN', 'ROLE_CUSTOMER_SERVIC'))) {
                            $contact = new AllContact();

                            $contact->setInvitation(true)
                                ->setFirstName($createdBy->getFirstName())
                                ->setLastName($createdBy->getLastName())
                                ->setNumber($createdBy->getPrimaryPhone()->getNumber())
                                ->setEmail($createdBy->getPrimaryEmail()->getEmail())
                                ->setUser($createdBy)
                                ->setStatus($this->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
                                ->setCreatedBy($user);
                            $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);
                            $user->addAllContact($contact);
                        }

                        // Information update with condition
                        if ($user->getRole()->getRole() === 'ROLE_VENDOR') {
                            $createdBy->setMyVendor($user);
                            $user->setVendorsWithMe($createdBy);
                            $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($createdBy);
                        }

                        // Information update with condition
                        if (
                            $createdBy->getRole()->getRole() !== 'ROLE_ADMIN'
                            && $invite->getRole()->getRole() !== 'ROLE_VENDOR'
                        ) {
                            $business = $createdBy->getBusinesses()->first();
                            $user->addBusiness($business);
                            $business->addStaff($user);
                            $em->getRepository('LocalsBest\UserBundle\Entity\Business')->save($business);
                        }
                    }
                }

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                if (is_null($user->getRole())) {
                    $user->setRole($em->getRepository('LocalsBestUserBundle:Role')->findOneBy(['level' => 4]));
                }
                $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);

                // Send Welcome Email
                $this->getMailMan()->sendWelcomeMail($user);
                // Show flash message
                $this->addSuccessMessage('Account created successfully.Proceed to login');
                // Redirect to Login Page
                return $this->redirect($this->generateUrl('login'));
            }
        }

        return [
            'form'      => $form->createView(),
            'invited'   => $invited
        ];
    }

    /**
     * Display Forgot Password Page
     *
     * @param Request $request
     * @return array|RedirectResponse
     * @Template
     */
    public function forgotPasswordAction(Request $request)
    {
        // If User is Loged in redirect to Home page
        if ($this->getUser()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $error = '';
        // Create Forgot Password Form object
        $form = $this->createForm(ForgotType::class);

        // If Forgot Password Form was submit
        if ($request->getMethod() == 'POST') {
            //Put request information to Forgot Password Form
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Get entered username
                $username = $form->get('username')->getData();

                $em = $this->getDoctrine()->getManager();
                // Look for User Entity using username
                $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')->findOneBy(['username' => $username]);

                // If User Entity exists
                if ($user) {
                    // Generate token for Reset Password Page link
                    $token = $this->_generateToken();
                    // Set token to User Entity
                    $user->setToken($token);

                    // Create Note about password reset
                    $this->createNote($em, $user, 'There was request for Password Recovery that was send to your email');

                    // Update User Entity
                    $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                    // Send email with Link for Reset Password Page
                    $this->getMailMan()->sendPasswordResetMail($user);
                    // Show flash message
                    $this->addSuccessMessage('Password reset instructions sent to mail');
                    // Redirect to Login Page
                    return $this->redirect($this->generateUrl('login'));
                } else {
                    // Add error to Forgot Password Form
                    $form->get('username')->addError(new FormError('Username not found'));
                    $error = 'Username not found. Please enter correct one';
                }
            }
        }
        return array(
            'form' => $form->createView(),
            'error' => $error
        );
    }

    /**
     * Create note from Admin and share it with User
     *
     * @param ObjectManager $em
     * @param User $user
     * @param string $message
     */
    private function createNote(ObjectManager $em, User $user, $message="New Note")
    {
        $admin = $em->getRepository('LocalsBestUserBundle:User')->find(1);

        $note = new Note();
        $note->setStatus($em->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus());
        $note->setNote($message)
            ->setPrivate(true)
            ->setObjectType('LocalsBestUserBundle:User')
            ->setObjectId($user->getId())
            ->setUser($admin)
            ->setCreatedBy($admin);

        $em->persist($note);
        $em->flush();

        $share = new Share();
        $share->setUser($user);

        $token = base64_encode(time() . ':' . rand());

        $share->setToken($token);
        $share->setObjectType(ObjectTypeType::User);
        $share->setCreatedBy($admin);
        $share->setObjectId($user->getId());

        $user->addShare($share);
        $note->addShare($share);

        $em->flush();
    }

    /**
     * Generate Token for registered user
     * Token include current time and a random generated key
     *
     * @return string
     */
    protected function _generateToken()
    {
        $time = time();
        $randNum = rand();
        $token = base64_encode($time . ':' . $randNum);

        return $token;
    }

    /**
     * Display Reset Password Page
     *
     * @param Request $request
     * @param string $token
     * @return array|RedirectResponse
     * @Template
     */
    public function resetPasswordAction(Request $request, $token)
    {
        // Look for User Entity using token
        $user = $this->getDoctrine()->getRepository('LocalsBest\UserBundle\Entity\User')
            ->findOneBy(['token' => $token] );

        // If User Entity not exists throw Exception
        if (!$user) {
            $this->addFlash('danger', 'You tried to use Invalid token. Please try to reset it one more time.');
            return $this->redirectToRoute('forget_password');
        }

        $decodeToken = base64_decode($user->getToken());
        $filterToken = explode(':', $decodeToken);
        $registrationTime = $filterToken[0];

        $tokenExpiry = $this->container->getParameter('token_expiry');
        $validRegistrationTime = (time() - $tokenExpiry);

        // If token valid
        if ($registrationTime >= $validRegistrationTime) {
            // Create Reset Password Form object
            $form = $this->createForm(ResetType::class);

            // If Reset Password Form was submit
            if ($request->getMethod() == 'POST') {
                // set request information to Reset Password Form object
                $form->handleRequest($request);

                if ($form->isValid()) {
                    // Get New Entered Password
                    $newPassword = $form->get('password')->getData();

                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    // encode New Entered Password
                    $password = $encoder->encodePassword($newPassword, $user->getSalt());
                    // Set encoded Password for User Entity
                    $user->setPassword($password);
                    // once password reset, set token to null
                    $user->setToken(null);

                    // Save User Entity
                    $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                    // Show flash message
                    $this->addSuccessMessage('Password changed successfully.Proceed to login now');
                    // Redirect user to Login Page
                    return $this->redirect($this->generateUrl('login'));
                }
            }
        } else {
            $this->addFlash('danger', 'You tried to use Expired token. Please try to reset it one more time.');
            return $this->redirectToRoute('forget_password');
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Page to register User as Client for selected User
     *
     * @param Request $request
     * @param $referralId
     * @return RedirectResponse|Response
     */
    public function clientRegisterAction(Request $request, $referralId)
    {
        // User that already login can not open this page
        if ($this->getUser()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        // Get Doctrine Entity Manager
        $em = $this->getDoctrine()->getManager();

        /**
         * User who invite client
         *
         * @var User $referral
         */
        $referral = $em->getRepository('LocalsBestUserBundle:User')->find($referralId);

        // If User was not found throw 404
        if ($referral === null) {
            throw $this->createNotFoundException();
        }

        /**
         * Get Business of person who send invite link
         *
         * @var Business $business
         */
        $business = $referral->getBusinesses()->first();

        /** @var User $user */
        $user = new User();
        // Create object of client role
        $role = $em->getRepository('LocalsBest\UserBundle\Entity\Role')->find(9);
        // Set role to user
        $user->setRole($role);
        // Set Person who invite Client
        $user->setCreatedBy($referral);
        // Add empty Email and empty Phone entities
        $user->getContact()->addEmail(new Email());
        $user->getContact()->addPhone(new Phone());

        // Create Register Form object
        $form = $this->createForm(RegisterType::class, $user);
        // If Register Form was submit
        if ($request->isMethod('POST')) {

            $em->getFilters()->disable('softdeleteable');

            // Merge Form and Request data
            $form->handleRequest($request);

            $clientBusinessName = $request->request->get('client_business_name', null);

            // Check form for Valid
            if ($form->isValid() && ($clientBusinessName !== null && $clientBusinessName != '')) {
                $user = $form->getData();
                // Create hash for User password
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                // Attach Client to User Business
                $user->addBusiness($business);
                $business->addStaff($user);

                $clientBusiness = new ClientBusiness();
                $clientBusiness->setName($clientBusinessName);
                $clientBusiness->setUser($user);

                $em->persist($clientBusiness);
                // Save User
                $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);
                $em->flush();

                if ($referralId == 2326) {
                    $this->getMailMan()->sendASSignWelcomeMail($user);
                } else {
                    $this->getMailMan()->sendWelcomeMail($user);
                }

                // Send message to User
                $this->addFlash(
                    'success',
                    'Client account for ' . $business->getName() . ' create successfully.'
                );
                // Redirect User to Login page
                return $this->redirectToRoute('login');
            }
        }

        $isClientBusNameError = isset($clientBusinessName)&&($clientBusinessName===null || $clientBusinessName=='');

        // render view with params
        return $this->render('@LocalsBestUser/auth/client-register.html.twig', [
            'isClientBusinessNameError' => $isClientBusNameError,
            'business' => $business,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * New Registration Process
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newRegisterAction(Request $request)
    {
        // Create Form Object
        $form = $this->createForm(NewRegisterType::class, null, [
            'attr' => [
                'id' => 'submit_form',
            ]
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                // Get User Type from form
                $userType = (int)$request->request->get('new_register')['user1']['userType'];
                /**
                 * Gt User Object from form
                 * @var User $user1
                 */
                $user1 = $form['user1']->getData();

                // Attach primary email to User
                $pEmail = $user1->getPrimaryEmail();
                $pEmail->setContact($user1->getContact());

                // Attach primary phone to User
                $pPhone = $user1->getPrimaryPhone();
                $pPhone->setContact($user1->getContact());

                // Encrypt User password
                $factory = $this->get('security.encoder_factory');
                $encoder1 = $factory->getEncoder($user1);
                $password1 = $encoder1->encodePassword($user1->getPassword(), $user1->getSalt());
                $user1->setPassword($password1);

                if ($userType == 1) {
                    // Set Manager Role for User
                    $user1->setRole($em->getReference('LocalsBestUserBundle:Role', 4));
                } else {
                    // Set Agent Role for User
                    $user1->setRole($em->getReference('LocalsBestUserBundle:Role', 7));

                    /**
                     * Get data for second User (business manager)
                     * @var User $user2
                     */
                    $user2Data = $form['user2']->getData();

                    // Create User Object for second user (business manager)
                    $user2 = new User();

                    $user2->setFirstName($user2Data['firstName']);
                    $user2->setLastName($user2Data['lastName']);

                    $user2->setRole($em->getReference('LocalsBestUserBundle:Role', 4));
                    $user2->setCreatedBy($em->getReference('LocalsBestUserBundle:User', 1));

                    /** @var Email $pEmail2 */
                    $pEmail2 = $user2Data['primaryEmail'];
                    $pEmail2->setContact($user2->getContact());
                    $user2->setPrimaryEmail($pEmail2);

                    /** @var Phone $pPhone2 */
                    $pPhone2 = $user2Data['primaryPhone'];
                    $pPhone2->setContact($user2->getContact());
                    $user2->setPrimaryPhone($pPhone2);

                    // Generate unique username
                    do {
                        $randomUsername = $this->generateRandomString();

                        $usernameCheck = $em->getRepository('LocalsBestUserBundle:User')
                            ->findOneBy(['username' => $randomUsername]);
                    } while ($usernameCheck !== null);

                    $user2->setUsername($randomUsername);

                    // Generate and encrypt user password
                    $randomPassword = $this->generateRandomString(16);
                    $encoder2 = $factory->getEncoder($user2);
                    $password2 = $encoder2->encodePassword($randomPassword, $user2->getSalt());
                    $user2->setPassword($password2);

                    $user1->setCreatedBy($user2);
                }

                /** @var Business $business */
                $business = $form['business']->getData();

                // Set owner for Business
                if ($userType == 1) {
                    $business->setOwner($user1);
                    $user1->setOwner($business);
                } else {
                    $business->setOwner($user2);
                    $user2->setOwner($business);
                }

                if ($userType == 2 && isset($user2)) {
                    // Add User to Business Staff
                    $business->addStaff($user2);
                    $business->setIsClaimed(false);
                    $user2->addBusiness($business);

                    // Save User
                    $em->persist($user2);

                    // Send Email with Credentials to User
                    $this->get('localsbest.mailman')->sendNewManagerRegister(
                        $user2, $user1, ['username' => $randomUsername, 'password' => $randomPassword]
                    );

                    // Send Welcome Email
                    $this->getMailMan()->sendWelcomeMail($user2);
                }

                // Get Default Products Module
//                $module = $em->getRepository('LocalsBestUserBundle:ProductsModule')
//                    ->findOneBy([
//                        'isDefault' => true,
//                    ]);
//
//                // If default bundle exists - set it to new business
//                if ($module !== null) {
//                    $module->addBusiness($business);
//                }

                // Add User to Business Staff
                $business->addStaff($user1);
                $user1->addBusiness($business);

                // Save User
                $em->persist($user1);

                // Send Welcome Email
                $this->getMailMan()->sendWelcomeMail($user1);

                // Save Business
                $em->persist($business);

                $em->flush();

                // Send Email after Business Create
                $this->getMailMan()->sendMail(
                    'EasyCloses Instructions to All Vendors',
                    '@LocalsBestNotification/mails/create-vendor-business.html.twig',
                    ['user' => (isset($user2) ? $user2 : $user1)],
                    [(isset($user2) ? $user2->getPrimaryEmail()->getEmail() : $user1->getPrimaryEmail()->getEmail())]
                );

                // Redirect User to Login Page
                return $this->redirectToRoute('login');
            }
        }
            // Render Page Layout
            return $this->render('@LocalsBestUser/auth/new-register.html.twig', [
                'form' => $form->createView()
            ]);
        }


    /**
     * Generate Random String
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
