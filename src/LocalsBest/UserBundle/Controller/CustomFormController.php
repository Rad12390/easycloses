<?php

namespace LocalsBest\UserBundle\Controller;

use DateTime;
use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Dbal\Types\ObjectTypeType;
use LocalsBest\UserBundle\Entity\Address;
use LocalsBest\UserBundle\Entity\CustomForm;
use LocalsBest\UserBundle\Entity\DocumentJob;
use LocalsBest\UserBundle\Entity\DocumentUser;
use LocalsBest\UserBundle\Entity\Job;
use LocalsBest\UserBundle\Entity\JobContact;
use LocalsBest\UserBundle\Entity\Property;
use LocalsBest\UserBundle\Entity\Share;
use LocalsBest\UserBundle\Entity\TransactionProperty;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomFormController extends SuperController
{
    /**
     * Display Sing Form Page
     *
     * @param Request $request
     * @return Response
     */
    public function signFormAction(Request $request)
    {
        // Get Request data
        $data = $request->query->all();

        // check transaction for this form
        if(isset($data['transactionId'])) {
            $transactionId = $data['transactionId'];
        } else {
            $transactionId = null;
        }

        // get current user
        $currUser = $this->getUser();

        // create array with user data

        if ($currUser->getRole()->getLevel() == 8) {
            $company = $currUser->getClientBusiness() !== null ? $currUser->getClientBusiness()->getName() : '';
        } else {
            $company = $this->getBusiness()->getName();
        }

        $user = [
            'firstName' => $currUser->getFirstName(),
            'lastName' => $currUser->getLastName(),
            'email' => $currUser->getPrimaryEmail()->getEmail(),
            'phone' => $currUser->getPrimaryPhone()->getClearNumber(),
            'company' => $company,
        ];

        // create array with client data
        $client = [
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'phone' => '',
            'company' => '',
        ];

        // check infomation from regular job
        if(count($data) > 0) {
            foreach ($data['job']['contacts'] as $contact) {
                if ($contact['role'] == 'Created by') {

                    $phone = str_replace(['(', ')', '-', ' '], '', $contact['phone']);
                    $separateName = explode(' ', $contact['contactName']);

                    $user = [
                        'firstName' => $separateName[0],
                        'lastName' => $separateName[1],
                        'email' => $contact['email'],
                        'phone' => $phone,
                        'company' => $contact['company'],
                    ];
                }

                if ($contact['role'] == 'Client') {

                    $phone = str_replace(['(', ')', '-', ' '], '', $contact['phone']);
                    $separateName = explode(' ', $contact['contactName']);

                    $client = [
                        'lastName' => array_pop($separateName),
                        'firstName' => implode(' ', $separateName),
                        'email' => $contact['email'],
                        'phone' => $phone,
                        'company' => $contact['company'],
                    ];
                }
            }
        }

        // render view
        return $this->render('@LocalsBestUser/custom_form/sign-form.html.twig', [
            'type' => 'sign-form',
            'data' => $data,
            'user' => $user,
            'client' => $client,
            'transactionId' => $transactionId,
        ]);
    }

    /**
     * Display Shredding Form
     *
     * @param Request $request
     * @param $userId
     * @return Response
     */
    public function shreddingFormAction(Request $request, $userId)
    {
        // Fet Doctrine Manager
        $em = $this->getDoctrine()->getManager();
        // Get user object using ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($userId);
        // Get Business of current user
        $business = $this->getBusiness();
        // get buines set using user and business information
        $businessUserSet = $em->getRepository('LocalsBestUserBundle:BusinessUserSet')->findOneBy([
            'user' => $user,
            'business' => $business,
        ]);
        // get property using user and format = 'business'
        $property = $em->getRepository('LocalsBestUserBundle:Property')->findOneBy([
            'user' => $user,
            'format' => 'business',
        ]);

        // by default address is empty
        $address = '';
        $customer = '';
        // if property is not empty get full address
        if($property !== null) {
            $address = $property->getAddress()->getFull();
            $customer = $property->getTitle();

            $customer = preg_replace( "/\r|\n/", " ", $customer );
            $address = preg_replace( "/\r|\n/", " ", $address );
        }

        // by default quickBook ID is empty
        $quickBookId = '';
        // if business set is not empty get quick book id
        if($businessUserSet !== null) {
            $quickBookId = $businessUserSet->getQuickBookId();
        }

        // Render view
        return $this->render(
            '@LocalsBestUser/custom_form/shredding-form.html.twig',
            [
                'user' => $user,
                'account' => $quickBookId,
                'address' => $address,
                'customer' => $customer,
            ]
        );
    }

    /**
     * Create PDF file from Request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function shreddingPdfAction(Request $request)
    {
        // Get Request data
        $data = $request->request->all();

        // get Doctrine Manager
        $em = $this->getDoctrine()->getManager();

        // Get user object using ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($data['generator']['id']);

        // get business object of current user
        $business = $this->getBusiness();

        // get DomPDF object
        $dompdf = $this->get('dompdf');

        // render html file for PDF Layout
        $html = $this->renderView('@LocalsBestUser/custom_form/shredding-pdf.html.twig',
            [
                'data' => $data,
            ]
        );

        // Attach html to DomPDF object
        $pdf = $dompdf->getPdf($html);

        // Create folder tree
        $path = $this->get('kernel')->getRootDir().'/../web/uploads';
        if (!is_dir($path)) {
            mkdir($path);
        }

        $path .= '/user-time-files';
        if (!is_dir($path)) {
            mkdir($path);
        }

        $path .= '/' . $user->getId();
        if (!is_dir($path)) {
            mkdir($path);
        }

        // Create filename
        $fileName = $user->getId() . '-shredding-form-' . time() . '.pdf';
        $filePath = $path . '/' . $fileName;

        // put PDF content to file
        file_put_contents($filePath, $pdf);

        $file = new File($filePath);

        // get AWS object
        $sdk = $this->get('aws_sdk');
        $s3  = $sdk->createS3();

        // upload PDF to AWS
        $result = $s3->putObject(array(
            'Bucket' => $this->getParameter('users_bucket'),
            'Key' => 'users/' . $user->getId() . '/' . $fileName,
            'Body' => fopen($filePath, 'r'),
            'ACL' => 'public-read'
        ));

        // Create document entity
        $document = new DocumentUser();

        // Set entity params
        $document->setStatus($this->getRepository('LocalsBestUserBundle:DocumentUser')->getDefaultStatus());
        $document->setCreatedBy($this->getBusiness()->getOwner());
        $document->setUser($user);
        $document->setOwner($business);
        $document->setFile($file);

        $document
            ->setFileName('users/' . $user->getId() . '/' . $fileName)
            ->setExtension($file->getExtension());

        // Attach document to user entity
        $user->addDocument($document);

        // Save document entity
        $this->getRepository('LocalsBestUserBundle:DocumentUser')->save($document);
        $this->getRepository('LocalsBestUserBundle:User')->save($user);

        $share = new Share();

        $share->setUser($user);
        $share->setStatus(1);

        $token   = base64_encode(time() . ':' . rand());
        $share->setToken($token);
        $share->setCreatedBy($this->getBusiness()->getOwner());

        $share
            ->setObjectType('LocalsBestUserBundle:DocumentUser')
            ->setObjectId($document->getId());

        $document->addShare($share);

        $em->persist($share);
        $em->flush();

        $this->get('localsbest.notification')
            ->addNotification('New Document shared with you',
                'document_share_response', array('token' => $share->getToken()),
                [$user], array($share->getUser()));

        // define recipients array
        $recipients = [];

        // add current user and selected user to recipients
        $recipients[] = $user->getPrimaryEmail()->getEmail();

        // get business recipients
        $businessRecipients = $em->getRepository('LocalsBestUserBundle:BusinessRecipient')
            ->findBy(['business' => $business]);

        // set business recipients to local recipients list
        foreach ($businessRecipients as $recipient) {
            $recipients[] = $recipient->getEmail();
        }

        $emailTemplate = $em->getRepository('LocalsBestUserBundle:EmailTemplate')->findOneBy(
            ['business' => $business, 'category' => 'Shredding PDF', 'template_number' => 1]
        );

        // for each recipient send email
        foreach ($recipients as $recipient) {
            $body = str_replace(
                [

                ],
                [

                ],
                $emailTemplate->getEmailBody()
            );

            $this->getMailMan()->sendShreddingPdf($recipient, $document, $body, $emailTemplate->getEmailTitle());
        }

        // remove file from server
        unlink($filePath);

        // return json response
        return new JsonResponse([
            'result' => 1,
            'userUrl' => $this->generateUrl('user_view', ['username' => $user->getUsername()]),
        ]);
    }

    /**
     * Save Sign Form Request to DB
     *
     * @param Request $request
     * @param $type
     * @return JsonResponse
     */
    public function storeAction(Request $request, $type)
    {
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $time = new DateTime('now');

        $form = new CustomForm();

        $form->setContent(json_encode($data));
        $form->setCreatedBy($this->getUser());
        $form->setCreatedAt($time);
        $form->setType($type);

        $em->persist($form);
        $em->flush();

        if($data['total'] == 0 && $data['sign_removal']['status'] == true) {
            $this->createJobFromSignForm($data);

            return new JsonResponse([
                'url' => $this->generateUrl('locals_best_user_homepage'),
                'message' => 'New Job created successfully',
                'title' => 'Done!'
            ]);
        }

        return new JsonResponse([
            'url' => $this->generateUrl('custom_form_payment', [
                'type' => $type,
                'id' => $form->getId(),
            ]),
            'message' => 'You must pay Now to complete this order',
            'title' => 'Almost Done!'
        ]);
    }

    /**
     * Display Payment Page for Sign Form
     *
     * @param Request $request
     * @param $type
     * @param int $id
     * @return Response
     */
    public function paymentAction(Request $request, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $em->getRepository('LocalsBestUserBundle:CustomForm')->find($id);

        $data = json_decode($form->getContent());

        return $this->render(
            '@LocalsBestUser/custom_form/payment.html.twig',
            [
                'data' => $data,
                'user' => $this->getUser(),
                'type' => $type,
                'id' => $id,
            ]
        );
    }

    /**
     * Charge sign form
     *
     * @param Request $request
     * @param $type
     * @param $id
     * @return RedirectResponse
     */
    public function chargeFormAction(Request $request, $type, $id)
    {
        // Get Doctrine Manager
        $em = $this->getDoctrine()->getManager();

        // Get Custom Form Object
        $form = $em->getRepository('LocalsBestUserBundle:CustomForm')->find($id);

        // Get Request Data
        $paymentData = $request->request->all();

        // Decode content of custom form object
        $data = json_decode($form->getContent(), true);

        // Get Data for Authorize
        $cardNumber = $paymentData['credit_card_number'];
        $cvv2 = $paymentData['cvv2'];
        $expirationDate = $paymentData['expiration_year'] . '-' . $paymentData['expiration_month'];
        $amount = $data['total'];

        // Get user email
        $userEmail = $paymentData['user_email'];

        // Define file for logs
        define("AUTHORIZENET_LOG_FILE", '../app/logs/payment_copy.log');

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getParameter('sign_form_auth_login_id'));
        $merchantAuthentication->setTransactionKey($this->getParameter('sign_form_auth_transaction_key'));
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expirationDate);
        $creditCard->setCardCode($cvv2);

        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        $order = new AnetAPI\OrderType();
        $order->setDescription(
            'Sign Form from EasyCloses Payment: ' .
            'Order #' . $id . '; ' .
            $paymentData['first_name'] . ' ' . $paymentData['last_name'] . '; ' .
            $userEmail . '; '
        );

        $billToSingle = new AnetAPI\CustomerAddressType();
        $billToSingle->setFirstName($paymentData['first_name']);
        $billToSingle->setLastName($paymentData['last_name']);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setEmail($userEmail);

        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($billToSingle);
        $transactionRequestType->setCustomer($customerData);

        $requestP = new AnetAPI\CreateTransactionRequest();
        $requestP->setMerchantAuthentication($merchantAuthentication);
        $requestP->setRefId( $refId);
        $requestP->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($requestP);
        $response = $controller->executeWithApiResponse( ANetEnvironment::PRODUCTION);

        if ($response != null) {
            if($response->getMessages()->getResultCode() == 'Ok') {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $jobId = $this->createJobFromSignForm($data);

                    if (isset($data['image'])) {
                        unset($data['image']);
                    }

                    $form
                        ->setPaymentStatus('paid')
                        ->setPaymentTransactionId($tresponse->getTransId())
                        ->setContent(json_encode($data));

                    $em->flush();

                    $this->addFlash('success', "Job created successfully!");

                    if (isset($data['transactionId'])) {
                        return $this->redirectToRoute('transaction_view', ['id' => $data['transactionId']]);
                    } else {
                        return $this->redirectToRoute('job_view', ['id' => $jobId]);
                    }
                } else {
                    if($tresponse->getErrors() != null) {
                        $business = $em->getRepository('LocalsBestUserBundle:Business')->find(173);

                        $this->getMailMan()->paymentErrorMail(
                            [
                                'user' => $this->getUser()->getFullName(),
                                'item' => 'Sign Form',
                                'code' => $tresponse->getErrors()[0]->getErrorCode(),
                                'message' => $tresponse->getErrors()[0]->getErrorText(),
                            ],
                            $business->getOwner()->getPrimaryEmail()->getEmail()
                        );
                        $this->addFlash('danger', $tresponse->getErrors()[0]->getErrorCode().": ".$tresponse->getErrors()[0]->getErrorText());
                        $referer = $request->headers->get('referer');
                        return $this->redirect($referer);
                    } else {
                        $business = $em->getRepository('LocalsBestUserBundle:Business')->find(173);

                        $this->getMailMan()->paymentErrorMail(
                            [
                                'user' => $this->getUser()->getFullName(),
                                'item' => 'Sign Form',
                                'code' => 'n/a',
                                'message' => 'Undefined error',
                            ],
                            $business->getOwner()->getPrimaryEmail()->getEmail()
                        );
                        $this->addFlash('danger', "Undefined error");
                        $referer = $request->headers->get('referer');
                        return $this->redirect($referer);
                    }
                }
            } else {
                $tresponse = $response->getTransactionResponse();

                if($tresponse != null && $tresponse->getErrors() != null) {
                    $business = $em->getRepository('LocalsBestUserBundle:Business')->find(173);

                    $this->getMailMan()->paymentErrorMail(
                        [
                            'user' => $this->getUser()->getFullName(),
                            'item' => 'Sign Form',
                            'code' => $tresponse->getErrors()[0]->getErrorCode(),
                            'message' => $tresponse->getErrors()[0]->getErrorText(),
                        ],
                        $business->getOwner()->getPrimaryEmail()->getEmail()
                    );
                    $this->addFlash('danger', $tresponse->getErrors()[0]->getErrorCode().": ".$tresponse->getErrors()[0]->getErrorText());
                    $referer = $request->headers->get('referer');
                    return $this->redirect($referer);
                } else {
                    $business = $em->getRepository('LocalsBestUserBundle:Business')->find(173);

                    $this->getMailMan()->paymentErrorMail(
                        [
                            'user' => $this->getUser()->getFullName(),
                            'item' => 'Sign Form',
                            'code' => $response->getMessages()->getMessage()[0]->getCode(),
                            'message' => $response->getMessages()->getMessage()[0]->getText(),
                        ],
                        $business->getOwner()->getPrimaryEmail()->getEmail()
                    );
                    $this->addFlash('danger', $response->getMessages()->getMessage()[0]->getCode().": ".$response->getMessages()->getMessage()[0]->getText());
                    $referer = $request->headers->get('referer');
                    return $this->redirect($referer);
                }
            }
        } else {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->find(173);

            $this->getMailMan()->paymentErrorMail(
                [
                    'user' => $this->getUser()->getFullName(),
                    'item' => 'Sign Form',
                    'code' => 'n/a',
                    'message' => 'No response returned',
                ],
                $business->getOwner()->getPrimaryEmail()->getEmail()
            );
            $this->addFlash('danger', "No response returned");
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
    }

    /**
     * Create Job Object and Attache Document Object for it from request information
     *
     * @param $data
     * @return int
     */
    private function createJobFromSignForm($data)
    {
        // Get Doctrine Manager
        $em = $this->getDoctrine()->getManager();

        // Create New Job Object
        $job = new Job();

        // If transaction ID set
        if(isset($data['transactionId'])) {
            // Get Transaction object
            $transaction = $em->getRepository('LocalsBestUserBundle:Transaction')->find($data['transactionId']);

            // Get TransactionProperty Object
            $jobProperty = $transaction->getTransactionProperty();

            // Attach TransactionProperty Object to new Job Object
            $job->setTransaction($transaction);
        } else {
            // Create ne Address Object
            $address = new Address();

            // Set Address Object properties
            $address
                ->setStreet($data['address']['street'])
                ->setState($data['address']['state'])
                ->setCity($data['address']['city'])
                ->setZip($data['address']['zip'])
            ;

            // Save object
            $em->persist($address);

            // Create new Property Object
            $property = new Property();

            // Attache Address Object to Property Object
            $property->setAddress($address);

            // Save Object
            $em->persist($property);

            // Create New TransactionProperty Object
            $jobProperty = new TransactionProperty();

            // Set TransactionProperty Object properties
            $jobProperty
                ->setProperty($property)
                ->setMlsBoard($data['address']['unit'])
            ;
            // Save Object
            $em->persist($jobProperty);
        }

        // Get current Business Object
        $business = $this->getBusiness();

        // Get Vendor (User) Object by ID
        $vendor = $em->getRepository('LocalsBestUserBundle:User')->find(2326);

        $currUser = $this->getUser();

        $currentDate = new DateTime('now');
        // Set Job properties
        $job
            ->setDueDate($currentDate->modify('+5 days'))
            ->setJobProperty($jobProperty)
            ->setOrderType('Order')
            ->setJobStatus('New')
            ->setIsParent(true)
            ->setIndustryType($em->getReference('\LocalsBest\UserBundle\Entity\IndustryType', 108))
            ->setStatus($this->getRepository('LocalsBestUserBundle:Job')->getDefaultStatus())
            ->setCreatedBy($currUser)
            ->setOwner($business);

        // Create Job Contact Object
        $jobCreatedByContact = new JobContact();

        // Set Job Contact properties
        $jobCreatedByContact
            ->setRole('Created by')
            ->setCompany($data['user']['business'])
            ->setContactName($data['user']['firstName'] . ' ' . $data['user']['lastName'])
            ->setEmail($data['user']['email'])
            ->setPhone($data['user']['phone']['areaCode'] . $data['user']['phone']['number'])
            ->setContactBy('phone')
            ->setJob($job);

        // Save object
        $em->persist($jobCreatedByContact);

        // Attach Job Contact to Job
        $job->addContact($jobCreatedByContact);

        // Create Job Contact Object
        $jobClientContact = new JobContact();

        $jobClientContact
            ->setRole('Client')
            ->setCompany($data['user']['business'])
            ->setContactName($data['user']['firstName'] . ' ' . $data['user']['lastName'])
            ->setEmail($data['user']['email'])
            ->setPhone($data['user']['phone']['areaCode'] . $data['user']['phone']['number'])
            ->setContactBy('phone')
            ->setJob($job);;

        // Save object
        $em->persist($jobClientContact);

        // Attach Job Contact to Job
        $job->addContact($jobClientContact);

        // Save object
        $em->persist($job);

        // Get clone of Job object
        $jobVendor = clone $job;

        // Set properties for Job clone
        $jobVendor
            ->setIsParent(false)
            ->setParent($job)
            ->setVendor($vendor)
        ;

        // Set Job Contacts for Job clone
        foreach ($job->getContacts() as $contact) {
            if ($jobVendor->getContacts()->contains($contact)) {
                $jobVendor->removeContact($contact);
            }

            $newContact = new JobContact();
            $newContact->setRole($contact->getRole())
                ->setPhone($contact->getPhone())
                ->setEmail($contact->getEmail())
                ->setCompany($contact->getCompany())
                ->setContactName($contact->getContactName())
                ->setContactBy($contact->getContactBy())
                ->setJob($jobVendor);
            $em->persist($newContact);
            $jobVendor->addContact($newContact);
        }

        //Add vendor like contact to job parties
        $jobVendorContact = new JobContact();
        $jobVendorContact
            ->setRole($jobVendor->getIndustryType()->getName())
            ->setCompany($vendor->getOwner()->getName())
            ->setContactName($vendor->getOwner()->getContactName())
            ->setEmail($vendor->getPrimaryEmail()->getEmail())
            ->setPhone($vendor->getPrimaryPhone()->getNumber())
            ->setContactBy('phone')
            ->setJob($jobVendor);
        // Save object
        $em->persist($jobVendorContact);

        // Add Job Contact to Job
        $jobVendor->addContact($jobVendorContact);

        // Save object
        $em->persist($jobVendor);

        // Update DB
        $em->flush();

        // Create message text for Note object
        $message = '';

        if($data['sign_installation']['status'] != 'false') {
            $currUser->setAssignInventory($currUser->getAssignInventory()-(int)$data['sign_installation']['quantity']);

            $message .= 'Sign Installation: ' . $data['sign_installation']['quantity'] . '; ';
        }

        if($data['sign_removal']['status'] != 'false') {
            $currUser->setAssignInventory($currUser->getAssignInventory()+1);

            $message .= 'Sign Removal: Ordered; ';
        }

        if($data['rider_installation']['status'] != 'false') {
            $message .= 'Rider Installation: ' . $data['rider_installation']['quantity'] . '; ';
        }

        if($data['a_frame']['status'] != 'false') {
            $message .= 'A Frame: ' . $data['a_frame']['quantity'] . '; ';
        }

        if ($data['open_house_signs']['status'] != 'false') {
            $message .= 'Open House Signs: ' . $data['a_frame']['quantity'] . '; ';
        }

        if($data['sign_fabrication']['status'] != 'false') {
            if($data['sign_fabrication']['with_photo_quantity'] > 0) {
                $message .= 'Sign Fabrication With Photo: ' . $data['sign_fabrication']['with_photo_quantity'] . '; ';
            }
            if($data['sign_fabrication']['without_photo_quantity'] > 0) {
                $message .= 'Sign Fabrication Without Photo: ' . $data['sign_fabrication']['without_photo_quantity'] . '; ';
            }
        }

        if($data['bulk_sign_fabrication']['status'] != 'false') {
            if($data['bulk_sign_fabrication']['with_photo_quantity'] > 0) {
                $message .= 'Bulk Sign Fabrication With Photo: ' . $data['bulk_sign_fabrication']['with_photo_quantity'] . '; ';
            }
            if($data['bulk_sign_fabrication']['without_photo_quantity'] > 0) {
                $message .= 'Bulk Sign Fabrication Without Photo: ' . $data['bulk_sign_fabrication']['without_photo_quantity'] . '; ';
            }
        }

        if($data['rider_fabrication']['status'] != 'false') {
            if($data['rider_fabrication']['sold_quantity'] > 0) {
                $message .= 'Rider Fabrication SOLD: ' . $data['rider_fabrication']['sold_quantity'] . '; ';
            }

            if($data['rider_fabrication']['under_contract_quantity'] > 0) {
                $message .= 'Rider Fabrication UNDER CONTRACT: ' . $data['rider_fabrication']['under_contract_quantity'] . '; ';
            }

            if($data['rider_fabrication']['sale_pending_quantity'] > 0) {
                $message .= 'Rider Fabrication SALE PENDING: ' . $data['rider_fabrication']['sale_pending_quantity'] . '; ';
            }

            if($data['rider_fabrication']['for_sale_quantity'] > 0) {
                $message .= 'Rider Fabrication FOR SALE: ' . $data['rider_fabrication']['for_sale_quantity'] . '; ';
            }

            if($data['rider_fabrication']['family_2_quantity'] > 0) {
                $message .= 'Rider Fabrication FAMILY 2: ' . $data['rider_fabrication']['family_2_quantity'] . '; ';
            }

            if($data['rider_fabrication']['family_3_quantity'] > 0) {
                $message .= 'Rider Fabrication FAMILY 3: ' . $data['rider_fabrication']['family_3_quantity'] . '; ';
            }

            if($data['rider_fabrication']['pool_quantity'] > 0) {
                $message .= 'Rider Fabrication POOL: ' . $data['rider_fabrication']['pool_quantity'] . '; ';
            }

            if($data['rider_fabrication']['other_quantity'] > 0) {
                $message .= 'Rider Fabrication OTHER: ' . $data['rider_fabrication']['other_quantity'] . '; ';
            }

            if($data['rider_fabrication']['for_rent_quantity'] > 0) {
                $message .= 'Rider Fabrication FOR RENT: ' . $data['rider_fabrication']['for_rent_quantity'] . '; ';
            }
        }

        // Create Notification Object
        $this->get('localsbest.notification')
            ->addNotification(
                'New Job',
                'job_view',
                ['id' => $jobVendor->getId()],
                [$vendor],
                [$this->getUser()]
            )
        ;

        $message .= ' Total Amount: $' . $data['total'] . ';';

        // Add Instructions to Note if was set
        if ($data['instructions'] != '') {
            $message .= ' Instructions: ' . $data['instructions'];
        }


        if ($vendor->getPreference()->getMail() === TRUE) {
            // Send Email about New Job
            $this->getMailMan()->sendAsSignMailToVendorAboutNewJob($vendor, $jobVendor, $message);
        }

        // Create Note Object
        $noteObj = new Note();

        // Set object properties
        $noteObj->setStatus($this->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus());
        $noteObj
            ->setNote($message)
            ->setPrivate(true)
            ->setImportant(false)
            ->setObjectType('LocalsBestUserBundle:Job')
            ->setObjectId($jobVendor->getId())
            ->setUser($this->getUser())
            ->setCreatedBy($this->getUser())
            ->setOwner($business)
            ->setCreated(time())
        ;

        // Save object
        $em->persist($noteObj);

        // Attache Note object to Job
        $jobVendor->addNote($noteObj);

        // Create Share Object
        $share = new Share();
        $share->setUser($vendor);
        $token = base64_encode(time() . ':' . rand());

        $share->setToken($token);
        $share->setCreatedBy(isset($transaction) ? $transaction->getCreatedBy() : $this->getUser());
        $share->setObjectId($jobVendor->getId());
        $share->setObjectType(ObjectTypeType::Job);

        // Attache Share Object to Job
        $jobVendor->addShare($share);

        // Attache Share Object to User
        $noteObj->addShare($share);

        // Save Object
        $em->persist($noteObj);

        // Update DB
        $em->flush();

        // create and attache file from user if was sent
        if(isset($data['image']) && $data['image'] != '') {
            // Get current user
            $user = $this->getUser();

            // get encoded file content
            $imageCoddedContent = explode(',', $data['image']);
            // decode file content
            $imageContent = base64_decode($imageCoddedContent[1]);

            // create folder tree
            $path = $this->get('kernel')->getRootDir().'/../web/uploads';
            if (!is_dir($path)) {
                mkdir($path);
            }
            $path .= '/user-time-files';
            if (!is_dir($path)) {
                mkdir($path);
            }
            $path .= '/' . $user->getId();
            if (!is_dir($path)) {
                mkdir($path);
            }

            // create file name
            $fileName = $jobVendor->getId() . '-sign-form-file-' . time() . '.jpg';
            // get file path
            $filePath = $path . '/' . $fileName;
            // put content to new file
            file_put_contents($filePath, $imageContent);

            // create file object using new file
            $file = new File($filePath);

            // get AWS object
            $sdk = $this->get('aws_sdk');
            $s3  = $sdk->createS3();

            // Upload file to AWS
            $result = $s3->putObject(array(
                'Bucket' => $this->getParameter('jobs_bucket'),
                'Key' => $jobVendor->getId() . '/' . $fileName,
                'Body' => fopen($filePath, 'r'),
                'ACL' => 'public-read'
            ));

            // Create Document object for Job
            $document = new DocumentJob();
            // Set information for new Object
            $document->setStatus($this->getRepository('LocalsBestUserBundle:DocumentJob')->getDefaultStatus());
            $document->setCreatedBy($this->getUser());
            $document->setUser($user);
            $document->setOwner($business);
            $document->setFile($file);
            $document
                ->setFileName($jobVendor->getId() . '/' . $fileName)
                ->setExtension($file->getExtension());

            // Attach new Document to Job
            $jobVendor->addDocument($document);
            $document->setJob($jobVendor);

            // Save changes
            $this->getRepository('LocalsBestUserBundle:DocumentJob')->save($document);
            $this->getRepository('LocalsBestUserBundle:User')->save($user);

            // Remove file from server
            unlink($filePath);
        }
        // Return job ID
        return $jobVendor->getId();
    }
}
