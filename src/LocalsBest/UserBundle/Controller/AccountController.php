<?php

namespace LocalsBest\UserBundle\Controller;

use DateTime;
use Doctrine\ORM\ORMException;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\PaymentSplitSettings;
use LocalsBest\ShopBundle\Form\PaymentSplitSettingsType;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\ClientBusiness;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\AccountProfileType;
use LocalsBest\UserBundle\Form\BusinessType;
use LocalsBest\UserBundle\Form\ChangePasswordType;
use LocalsBest\UserBundle\Form\ClientBusinessType;
use LocalsBest\UserBundle\Form\ContactType;
use LocalsBest\UserBundle\Form\NewFieldsType;
use LocalsBest\UserBundle\Form\OverviewType;
use LocalsBest\UserBundle\Form\PaidPreferenceType;
use LocalsBest\UserBundle\Form\PreferenceType;
use Stripe\Error\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Display User Profile page
     *
     * @param Request $request
     * @param string $username
     * @return Response|RedirectResponse
     */
    public function profileAction(Request $request, $username = null)
    {
        //get the database manager
        $em = $this->getDoctrine()->getManager();
        // Get entity of current User
        $user = $this->getUser();

        $routeusername = null;

        if ($username) {
            if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
                $myStaffs = array();
                $businesses = $this->getUser()->getBusinesses();

                foreach($businesses as $business) {
                    $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                        ->findOneBy(array('id' => $business->getOwner()->getid()));
                    $myStaffs[] = $user;
                }
            }

            // Search user by username
            $user = $this->findOr404('LocalsBestUserBundle:User', array('username' => $username), 'No such user found');
            if ($user) {
                $accessToAdmin  = true;
                $routeusername = $username;
            } else {
                throw $this->createNotFoundException('Access Denied');
            }
        } else {
            $routeusername = $user->getUsername();
            $accessToAdmin  = false;
        }

        // Create Change Password Form object
        $changePasswordForm = $this->createForm(ChangePasswordType::class, null, [
            'action' => '#business_user_password'
        ]);

        $preference = $user->getPreference();
        // Create User Preference Form object
        $checker = $this->get('localsbest.checker');
        if ($checker->forAddon('notification settings text', $user)) {
            $preferenceForm   = $this->createForm(PaidPreferenceType::class, $preference);
        } else {
            $preferenceForm   = $this->createForm(PreferenceType::class, $preference, [
                'action' => '#personal_contact'
            ]);
        }

        // Create User Profile Form object
        $accountProfileForm = $this->createForm(AccountProfileType::class, $user, [
            'action' => '#personal_account',
            'user' => $user,
        ]);

        $ownBusiness = null;
        // get User Business Entity
        if ($user->getOwner() === null) {
            if (count($user->getBusinesses()) > 0) {
                $business = $user->getBusinesses()->first();
            } else {
                $business = new Business();
                $business->setOwner($user);
                $ownBusiness = 'isSet';
            }
        } else {
            $business = $user->getOwner();
            $ownBusiness = 'isSet';
        }

        // Create Business Form object
        $businessForm   =   $this->createForm(BusinessType::class, $business);
        // Create Business Contact Form object
        $businessContactForm = $this->createForm(ContactType::class, $business->getContact(), [
            'name' => 'business_contact',
        ]);

        // Get Users Properties
        $properties = $em->getRepository('LocalsBest\UserBundle\Entity\Property')->findBy(array('user' => $user));

        // Get Overview information Form
        $overviewForm = $this->createForm(OverviewType::class, $user, [
            'action' => '#personal_Overview',
            'user' => $user,
        ]);

        // Get Contact information Form
        $contactForm = $this->createForm(ContactType::class, $user->getContact(), [
            'action' => '#personal_contact'
        ]);

        // Create Form for Dates and Licenses fields
        $newFieldsForm = $this->createForm(NewFieldsType::class, $user, [
            'action' => '#new_fields'
        ]);

        // Create Form for Dates and Licenses fields
        $paymentSplitSettings = $this->createForm(PaymentSplitSettingsType::class, ($user->getPaymentSplitSettings() !== null ? $user->getPaymentSplitSettings() : new PaymentSplitSettings()), [
            'action' => '#split_settings',
            'user' => $this->getUser()
        ]);
        
        // Check Client Business Info
        if ($user->getClientBusiness() === null) {
            $clientBusiness = new ClientBusiness();
            $clientBusiness->setUser($user);
        } else {
            $clientBusiness = $user->getClientBusiness();
        }

        // Create Client Business Form
        $clientBusinessForm = $this->createForm(ClientBusinessType::class, $clientBusiness, [
            'action' => '#client_business'
        ]);

        // If Action get POST request
        if ($request->isMethod('POST')) {
            // Set Request Information to Forms
            $overviewForm->handleRequest($request);
            $changePasswordForm->handleRequest($request);
            $preferenceForm->handleRequest($request);
            $accountProfileForm->handleRequest($request);
            $businessForm->handleRequest($request);
            $contactForm->handleRequest($request);
            $businessContactForm->handleRequest($request);
            $newFieldsForm->handleRequest($request);
            $clientBusinessForm->handleRequest($request);
            $paymentSplitSettings->handleRequest($request);

            if ($overviewForm->isSubmitted()) {
                if ($overviewForm->isValid()) {
                    $overviewData = $overviewForm->getData();

                    if (!$username) {
                        $overviewData->setStatus($user->getStatus());
                    }

                    if ($business->getId() == 155) {
                        // Synchronize User Info With WP site
                        $this->synchronizeWithWP($overviewData);
                    }

                    $em->getRepository('LocalsBestUserBundle:User')->save($overviewData);

                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#personal_Overview'
                    );
                }
            }

            if ($changePasswordForm->isSubmitted()) {
                if ($changePasswordForm->isValid()) {
                    $password = $changePasswordForm->get('password')->getData();

                    // Encrypt new password and set to User
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($password, $user->getSalt());
                    $user->setPassword($password);
                    $em->getRepository('LocalsBest\UserBundle\Entity\User')->save($user);

                    $this->addFlash('success', 'Thank You, Your Password has been Updated');
                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#business_user_password'
                    );
                } else {
                    $this->addFlash('danger', 'Your Current or New Passwords don\'t match. Please Try Again');
                }
            }

            if ($preferenceForm->isSubmitted()) {
                if ($preferenceForm->isValid()) {
                    $preferenceData = $preferenceForm->getData();
                    $preferenceData->setUser($user);
                    $em->getRepository('LocalsBest\UserBundle\Entity\Preference')->save($preferenceData);

                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#personal_contact'
                    );
                }
            }

            if ($paymentSplitSettings->isSubmitted()) {
                if ($paymentSplitSettings->isValid()) {
                    $settings = $paymentSplitSettings->getData();
                    
                    if ($settings->getId() === null) {
                        $settings->setUser($user);
                        $em->persist($settings);
                    }
                    $em->flush();

                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#split_settings'
                    );
                }
            }

            if ($accountProfileForm->isSubmitted()) {
                if ($accountProfileForm->isValid()) {
                    $accountProfileData = $accountProfileForm->getData();

                    if ($business->getId() == 155) {
                        // Synchronize User Info With WP site
                        $this->synchronizeWithWP($accountProfileData);
                    }

                    $em->getRepository('LocalsBestUserBundle:User')->save($accountProfileData);

                    if($username !== null) {
                        $username = $accountProfileData->getUsername();
                    }

                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#personal_account'
                    );
                }
            }

            if ($businessForm->isSubmitted()) {
                if ($businessForm->isValid()) {
                    $business = $businessForm->getData();
                    $business->setUpdatedAt(new DateTime('now'));

                    if ($user->getOwnedBusiness()->isEmpty()) {
                        $user->addBusiness($business);
                        $user->setOwner($business);
                        $business->addStaff($user);
                    }
                    $em->getRepository('LocalsBest\UserBundle\Entity\Business')->save($business);
                    return $this->redirect($this->generateUrl('users_profile', array('username' => $username)));
                }
            }

            if ($contactForm->isSubmitted()) {
                if ($contactForm->isValid()) {
                    $contactData = $contactForm->getData();
                    $em->getRepository('LocalsBestUserBundle:Contact')->save($contactData);

                    $this->addSuccessMessage('Contact information updated successfully!');
                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#personal_contact'
                    );
                }
            }

            if ($businessContactForm->isSubmitted()) {
                if ($businessContactForm->isValid()) {
                    $contactData = $businessContactForm->getData();
                    $em->getRepository('LocalsBestUserBundle:Contact')->save($contactData);

                    $this->addSuccessMessage('Contact information updated successfully!');
                    return $this->redirect($this->generateUrl('users_profile', array('username' => $username)));
                }
            }

            if ($newFieldsForm->isSubmitted()) {
                if ($newFieldsForm->isValid()) {
                    $data = $newFieldsForm->getData();

                    $em->getRepository('LocalsBestUserBundle:User')->save($data);

                    $this->addSuccessMessage('Contact information updated successfully!');
                    return $this->redirect(
                        $this->generateUrl('users_profile', ['username' => $username]).'#new_fields'
                    );
                }
            }

            if ($clientBusinessForm->isSubmitted()) {
                if ($clientBusinessForm->isValid()) {
                    if ($clientBusiness->getId() === null) {
                        $em->persist($clientBusiness);
                    }
                    $em->flush();

                    return $this->redirect(
                        $this->generateUrl('users_profile', array('username' => $username)).'#client_business'
                    );
                }
            }
        }

        

        // Get Doc Rules for Current Business
        $docRules = $em->getRepository('LocalsBestUserBundle:DocRule')->findBy(['business' => $business]);

        // Get Vendor custom form for Job
        $formBuilder = json_decode($business->getBusinessForm(), true);
       
        //code to get the default charity selected
        
        $charityid=[];
        $charitytext=[];
        foreach($business->getTypes() as $type){
            if($type->getName()== 'Charity'){
               array_push($charityid,$business->getId());
               array_push($charitytext,$business->getName());
            }
        }
        // second default option for charity if user is invited by other
        if(empty($charitytext)){
            $invitor_user= $user->getCreatedBy();
            if(!empty($invitor_user)){
                if($invitor_user->getPaymentSplitSettings()!=null){
                    foreach($invitor_user->getPaymentSplitSettings()->getCharities() as $charity){
                        array_push($charitytext,$charity->getName());
                        array_push($charityid,$charity->getId());
                    }
                }
            }
        }
        $display= 0;
        if(empty($charitytext)){
            $invitor_user= $user->getCreatedBy();
            if(!empty($invitor_user)){
                $invitor_business= $invitor_user->getOwner();
                if(!empty($invitor_business)){
                    foreach($invitor_business->getTypes() as $type){
                        if($type->getName()== 'Charity'){
                           array_push($charityid,$invitor_business->getId());
                           array_push($charitytext,$invitor_business->getName());
                           $display=1;
                        }
                    }   
                }
            }
        }
        $payment_status=1;
        if($user->getPaymentSplitSettings()==NULL){
            $payment_status= 0;
        }
        
        $isempty=0;
        if($payment_status){
            if(empty($user->getPaymentSplitSettings()->getRebateStatus())){
                $isempty= 1;
            }
        }
        // Render view
        return $this->render('@LocalsBestUser/account/profile.html.twig', array(
            'form'                  => $overviewForm->createView(),
            'changePasswordForm'    => $changePasswordForm->createView(),
            'preferenceForm'        => $preferenceForm->createView(),
            'accountProfileForm'    => $accountProfileForm->createView(),
            'businessForm'          => $businessForm->createView(),
            'userProfile'           => $user,
            'overviewForm'          => $overviewForm->createView(),
            'routeusername'         => $routeusername,
            'properties'            => $properties,
            'stripeClientId'        => $this->getParameter('stripe_client_id'),
            'contactForm'           => $contactForm->createView(),
            'newFieldsForm'         => $newFieldsForm->createView(),
            'businessContactForm'   => $businessContactForm->createView(),
            'accessToAdmin'         => $accessToAdmin,
            'ownBusiness'           => $ownBusiness,
            'business'              => $business,
            'docRules'              => $docRules,
         //   'questions'             => $questions,
            'formBuilder'           => $formBuilder,
            'clientBusiness'        => $clientBusinessForm->createView(),
            'paymentSplitSettings'  => $paymentSplitSettings->createView(),
            'charityid'             => $charityid,
            'charitytext'           => $charitytext,
            'display'               => $display,
            'payment_status'        => $payment_status,
            'isempty'               => $isempty
         ));
    }

    /**
     * Create Stripe account for User
     *
     * @param $userId
     *
     * @return RedirectResponse
     */
    public function stripeAccountAction($userId){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($userId);
        if ($user) {
            try {
                $result = $this->get('app.client.stripe')->createStandardAccount($user->getPrimaryEmail(), $user->getFullName() );
                $user->setStripeAccountId($result->id);
                $user->setStripeAccountActivated(false);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Congrats: Your Account has been created Successfully on Stripe. Check your Primary Email Inbox and complete Stripe Account to Start receiving Payments'
                );
                return $this->redirectToRoute('users_profile');
            } catch (Base $e) {
                $this->addFlash('warning', sprintf('Unable to process, %s', $e->getMessage() ));
                return $this->redirectToRoute('users_profile');
            }
        }
        return $this->redirectToRoute('users_profile');
    }

    /**
     * Connect Stripe account with Ec account
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function connectAuthAction(Request $request){
        $error = $request->query->get('error');
        $code = $request->query->get('code');
        $state = $request->query->get('state');
        $id= '';

        if (isset($state) && !empty($state)) {
            $stateData = explode("-", $state);
            $id = $stateData[0];
        }
        if (isset($code) && !empty($code)) {
            try {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('LocalsBestUserBundle:User')->find($id);
                $result = $this->get('app.client.stripe')->getUserStripCreds($code);

                if (isset($result->stripe_user_id) && !empty($result->stripe_user_id)) {
                    if (
                        $result->accountdata->details_submitted == true
                        && $result->accountdata->charges_enabled == true
                    ) {
                        $user->setStripeAccountActivated(true);
                    }
                    $user->setStripeAccountId($result->stripe_user_id);
                    $em->flush();
                } elseif (isset($result->error) && !empty($result->error)) {
                    $this->addFlash('warning', sprintf('Unable to process, %s', $result->error_description ));
                }
                return $this->redirectToRoute('users_profile');
            }catch (ORMException $e) {
                $this->addFlash('warning', sprintf('Unable to process, %s', $e->getMessage() ));
                return $this->redirectToRoute('users_profile');
            }
        } elseif (isset($error) and !empty($error)) {
            $this->addFlash('warning', sprintf('Unable to process, %s', urldecode($request->query->get('error_description')) ));
        }
        return $this->redirectToRoute('users_profile');
    }

    /**
     * Activate Stripe account
     *
     * @throws Base
     */
    public function stripeAccountActivationAction()
    {
        $user = $this->getUser();
        if ($user->getStripeAccountActivated() == false || $user->getStripeAccountActivated() === null) {
            if ($user->getStripeAccountId() !== null) {
                $result = $this->get('app.client.stripe')->getStripeUserAccountData($user->getStripeAccountId());
                if (is_object($result)) {
                    if ($result->details_submitted == true || $result->charges_enabled == true) {
                        $user->setStripeAccountActivated(true);
                        $user->setStripeAccountId($result->id);
                        $this->getDoctrine()->getManager()->flush();
                        $this->addFlash('success', sprintf('Congrats: Your Stripe Account Now activated to Receive Payments' ));
                    }else {
                        $this->addFlash(
                            'warning',
                            sprintf('Note: Please Activate your Stripe Account to Receive Payments <a target="_blank" href"/profile#stripe_property">Link</a>' )
                        );
                    }
                }
            }
        }

        return $this->redirect($this->generateUrl('users_profile') . '#stripe_property');
    }

    /**
     * Send User info to WP size
     *
     * @param User $user
     * @return bool
     */
    private function synchronizeWithWP(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        if($user->getWpWebsiteUrl() === null || $user->getWpWebsiteUrl() == '') {

        } else {
            // Create URL
            $url = $user->getWpWebsiteUrl()
                . (substr($user->getWpWebsiteUrl(), -1) == '/' ? '' : '/')
                . 'wp-json/wp/v2/users'
            ;

            $wpCredentials = $user->getWpCredentials();

            // Credentials for WP site
            if ($user->getBusinesses()->first()->getId() == 155) {
                $username = "basil";
                $password = "147896325";
            } else {
                $username = $wpCredentials['wp_username'];
                $password = $wpCredentials['wp_password'];
            }

            // Generate secrete token
            if (
                isset($wpCredentials['wp_token'])
                && $wpCredentials['wp_token'] !== null
                && $wpCredentials['wp_token'] != ''
                && $user->getBusinesses()->first()->getId() != 155
            ) {
                $token = $wpCredentials['wp_token'];
            } else {
                $token = base64_encode($username.':'.$password);
            }

            $headers = ['Authorization:Basic '.$token];

            $params = [
                'username'          => $user->getUsername(),
                'first_name'        => $user->getFirstName(),
                'last_name'         => $user->getLastName(),
                'email'             => $user->getPrimaryEmail()->getEmail(),
                'password'          => $user->getUsername(),
                'role'              => $user->getRole()->getName(),
            ];

            if ($user->getWpAgentId() !== null) {
                $url .= '/' . $user->getWpAgentId();
                unset($params['email']);
            }

            // Send Request to WP API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            $resultJson = curl_exec($ch); // run the whole process

            if ($resultJson == false) {
                //var_dump($resultJson);echo curl_error($ch);die;
            }

            // Get Response Data
            $result = json_decode($resultJson);
            curl_close($ch);

            if ($user->getWpAgentId() === null || $user->getWpAgentId() == '') {
                $user->setWpAgentId($result->id);

                $em->flush();
            }

            // WP addition fields request
            $url = $user->getWpWebsiteUrl()
                . (substr($user->getWpWebsiteUrl(), -1) == '/' ? '' : '/')
                . 'wp-json/ec_api/v1/add_additional_fields_for_agent';

            $params = [
                'agent_id' => $user->getWpAgentId(),
                'facebook' => false,
                'twitter' => false,
                'linkedin' => false,
                'pinterest' => false,
                'instagram' => false,
                'website' => false,
                'phone' => $user->getPrimaryPhone()->getNumber(),
                'mobile' => false,
                'skype' => false,
                'title' => false,
                'custom_picture' => $user->getFileName() !== null ? 'https://s3.amazonaws.com/ec-users/' . $user->getFileName() : false,
                'small_custom_picture' => false,
                'user_agent_id' => '',
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'email' => $user->getPrimaryEmail()->getEmail(),
                'username' => $user->getUsername(),
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            $resultJson = curl_exec($ch); // run the whole process

            if ($resultJson == false) {
                return false;
            }

            json_decode($resultJson);
            curl_close($ch);
        }

        return true;
    }
}
