<?php

namespace LocalsBest\UserBundle\Controller;

use DateTimeZone;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\UserBundle\Entity\Address;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\City;
use LocalsBest\UserBundle\Entity\Commission;
use LocalsBest\UserBundle\Entity\Document;
use LocalsBest\UserBundle\Entity\DocumentType as EntityDocumentType;
use LocalsBest\UserBundle\Entity\DocumentUser;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\EventAlert;
use LocalsBest\UserBundle\Entity\Language;
use LocalsBest\UserBundle\Entity\ListingMoneyBox;
use LocalsBest\UserBundle\Entity\PaidInvite;
use LocalsBest\UserBundle\Entity\PlanRow;
use LocalsBest\UserBundle\Entity\Property;
use LocalsBest\UserBundle\Entity\Referral;
use LocalsBest\UserBundle\Entity\State;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\MainType;
use LocalsBest\UserBundle\Form\SupportType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class DefaultController extends Controller
{
    /**
     * Display Dashboard Page
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function mainAction()
    {

        // Check user password for default values
        if (!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }
        $isWithConcierge = $this->get('localsbest.checker')->forAddon('concierge service', $this->getUser());

        $em = $this->getDoctrine()->getManager();
        $biz = $this->getBusiness();

        if ($biz === null) {
            $this->addFlash(
                'danger',
                'Sorry, there problem with information about your business. Please, try to logout and login again.'
            );
            return $this->redirectToRoute('logout');
        }

        /** @var User $u */
        $u = $biz->getOwner();

        if (
            $this->getBusiness()->getTypes()->first()->getId() == 23
            || $this->getUser()->getRole()->getLevel() == 1
            || in_array($biz->getId(), [173])
        ) {
            $vendors = $this->getUser()->getVendorsByCategory2($u, 'all', false, $isWithConcierge);
            $form = $this->createForm(MainType::class, null, [
                'categories' => $this->getUser()->getAbleVendorCategories($vendors, true),
            ]);
        } else {
            $vUsers = [];
            $vUsersRaw = $u->getVendorsWithMe();
            foreach($vUsersRaw as $item) {
                $vUsers[] = $item->getId();
            }
            $vendors = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                ->findVendorsForVendor($u, implode(', ', $vUsers), 'all', $isWithConcierge);

            $form = $this->createForm(MainType::class, null,[
                'categories' => $u->getAbleVendorCategories($vendors),
            ]);
        }

        $myVendor = array();
        if (count($this->getUser()->getVendorsByCategory()) > 0) {
            $myVendor = $this->getUser()->getVendorsByCategory()->first()
                ->getBusinesses()->first()->getTypes()->first()->getId();
        }
        // Return params to view
        return [
            'business' => $biz,
            'form' => $form->createView(),
            'randomVendorCat' => $myVendor,
        ];
    }

   
    public function termsAction()
    {
        return $this->render('@LocalsBestUser/default/terms-and-conditions.html.twig');
    }

    /**
     * Display Join Page
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function joinAction(Request $request)
    {
        // Check user password for default values
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('locals_best_user_homepage'));
        }
        // Get token value
        $token = $request->query->get('token', null);
        $invite = null;
        $state = null;
        $type = null;

        $em = $this->getDoctrine()->getManager();

        if($token !== null) {
            // Get PaidInvite Entity
            /** @var PaidInvite $pInvite */
            $pInvite = $em->getRepository('LocalsBestUserBundle:PaidInvite')->findOneBy(['token' => $token]);

            if($pInvite !== null) {
                $invite = $pInvite;
                $type = 'paid';

                $shortStates = $invite->getCreatedBy()->getBusinesses()->first()->getAddress()->getState();
                // Get state name
                $state = $em->getRepository('LocalsBestUserBundle:State')
                    ->findOneBy(['short_name' => $shortStates])
                    ->getName();
            } else {
                // Get Free Invite
                /** @var PaidInvite $pInvite */
                $fInvite = $em->getRepository('LocalsBestUserBundle:Invite')->findOneBy(['token' => $token]);

                if($fInvite !== null) {
                    $invite = $fInvite;
                    $type = 'free';

                    $shortStates = $invite->getCreatedBy()->getBusinesses()->first()->getAddress()->getState();
                    // Get State name
                    $state = $em->getRepository('LocalsBestUserBundle:State')
                        ->findOneBy(['short_name' => $shortStates])
                        ->getName();
                }
            }
        }
        // Get All States
        $states = $em->getRepository('LocalsBestUserBundle:Business')->getStates();
        // Get All Industry Types
        $types = $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name' => 'ASC']);
        // Render view
        return $this->render(
            '@LocalsBestUser/default/join.html.twig',
            [
                'states'        => $states,
                'types'         => $types,
                'invite'        => $invite,
                'stateSelected' => $state,
                'iType'         => $type,
            ]
        );
    }

    /**
     * Return states by industry type
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxStatesByTypeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->query->get('id');
        $states = $em->getRepository('LocalsBestUserBundle:Business')->getStatesByBusinessIndType($id);

        return new JsonResponse($states);
    }

    /**
     * Get Business by state
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxBusinessesByStateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->query->get('id');
        $indId = $request->query->get('indType');

        $state = $em->getRepository('LocalsBestUserBundle:State')->find($id);
        $indType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($indId);
        $businesses = $em->getRepository('LocalsBestUserBundle:Business')->getBusinessByState($state);

        $result = [];

        /** @var Business $business */
        foreach($businesses as $business) {
            /** @var PlanRow $group */
            foreach ($business->getPlan()->getRows() as $group) {
                if($group->getIndustryGroup() == 6) {
                    continue;
                }

                if (
                    $group->getIndustryType()->contains($indType)
                    && (
                        ($group->getBronzePrice() + $group->getSetupBronzePrice() > 0)
                        || ($group->getSilverPrice() + $group->getSetupSilverPrice() > 0)
                        || ($group->getGoldPrice() + $group->getSetupGoldPrice() > 0)
                    )
                ) {
                    $result[] = ['id' => $business->getId(), 'name' => $business->getName()];
                    break;
                }
            }
        }

        return new JsonResponse($result);
    }

    /**
     * Get Plan by all params
     *
     * @param Request $request
     * @return Response
     */
    public function ajaxPlansByAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $businessId = $request->query->get('businessId');
        $indType = $request->query->get('indType');
        $token = $request->query->get('token', null);

        $business = $em->getRepository('LocalsBestUserBundle:Business')->find($businessId);
        $industryType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($indType);
        $plan = $business->getPlan();

        foreach($plan->getRows() as $group) {
            if($group->getIndustryType()->contains($industryType)) {
                $price = $group;
            }
        }

        $html = $this->renderView(
            '@LocalsBestUser/default/_plan-row.html.twig',
            [
                'price' => $price,
                'business' => $business,
                'indTypeId' => $indType,
                'token' => $token,
            ]
        );

        $response = new Response(json_encode( array("html" => $html) ));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Get Entity object by id
     *
     * @param string $object
     * @param int $id
     * @param int $createdBy
     * @return array|string
     * @Template
     */
    public function getEntityAction($object = null, $id = null, $createdBy = null)
    {
        
        if (is_null($object) || is_null($id)) {
            return '';
        }

        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository($object)->find($id);
        if(is_null($item)) {
            return '';
        }

        return [
            'object'    => $object,
            'id'        => $id,
            'item'      => $item,
            'createdBy' => $createdBy,
        ];
    }

    public function docCountAction($transactionId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Transaction $transaction */
        $transaction = $em->getRepository('LocalsBestUserBundle:Transaction')->find($transactionId);

        if(is_null($transaction)) {
            die('Can not find Transaction.');
        }

        /** @var User $user */
        $user = $transaction->getCreatedBy();

        if(is_null($user)) {
                die('Transaction doesn\'t have Owner.');
            }

        /** @var Business $business */
        $business = $user->getBusinesses()->first();

        if(is_null($business)) {
                die('User doesn\'t have Business.');
            }

        $prefix = $business->getId() . '/' . $transaction->getId() . '/';
        $bucket = $this->get('transactions_bucket');

        $sdk = $this->get('aws_sdk');
        $s3 = $sdk->createS3();

        $iterator = $objects = $s3->getIterator('ListObjects', array(
                'Bucket'    => $bucket,
                'Prefix'    => $prefix,
            ));

        $iterator = $s3->getIterator('ListObjects', array(
                'Bucket'    => $bucket,
                'Prefix'    => $prefix,
            ));

        echo 'Find documents for transaction ' . $transactionId . ': ' . iterator_count($iterator) . "\n";

        foreach ($objects as $object) {
                echo $object['Key'] . "\n";
        }

        die;
    }

    /**
     * Create support ticket for admin
     *
     * @param Request $request
     * @return array|RedirectResponse|Response
     * @Template
     */
    public function supportAction(Request $request)
    {
        $business = $this->getBusiness();

        $form = $this->createForm(SupportType::class);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $supportData = $form->getData();
                $supportData->setCreatedBy($this->getUser());

                if ($business != null) {
                    $supportData->setOwner($business);
                }

                $em = $this->getDoctrine()->getManager();

                $em->persist($supportData);
                $em->flush();

                $this->getMailMan()->sendServiceMail($supportData);

                return new JsonResponse(['success' => 1]);
            }
        }
        // return params to view
        return [
            'form' => $form->createView(),
            'business' => $business,
        ];
    }

    /**
     * Actions for importing information - Start
     */

    public function languageImportAction()
    {
        set_time_limit(0);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('languages list.xls');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . 'B' . $row, NULL, TRUE, FALSE);

            if ($rowData[0][1] == '') {
                break;
            }

            //  Insert row data array into your database of choice here

            $language = new Language();
            $language->setLanguage($rowData[0][1]);
            $this->getDoctrine()->getManager()->persist($language);
        }

        $this->getDoctrine()->getManager()->flush();

        die('done');
    }

    public function stateImportAction()
    {
        set_time_limit(0);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('states.xls');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        //  Loop through each row of the worksheet in turn
        for ($row = 1; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . 'C' . $row, NULL, TRUE, FALSE);

            if ($rowData[0][1] == '') {
                break;
            }

            //  Insert row data array into your database of choice here

            $state = new State();
            $state->setName(str_replace('"', '', $rowData[0][2]));
            $state->setShortName(str_replace('"', '', $rowData[0][1]));

            $this->getDoctrine()->getManager()->persist($state);
            $this->getDoctrine()->getManager()->flush();
        }

        die('done');
    }

    public function cityImportAction()
    {
        set_time_limit(0);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('cities_by_state.xls');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        //  Loop through each row of the worksheet in turn
        for ($row = 6947; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . 'C' . $row, NULL, TRUE, FALSE);

            if ($rowData[0][1] === 'NULL') {
                $rowData[0][1] = null;
            }

            $rowData[0][1] = str_replace('"', '', $rowData[0][1]);

            //  Insert row data array into your database of choice here
            $city = new City();
            $city->setName(str_replace('"', '', $rowData[0][2]));
            if (!is_null($rowData[0][1])) {
                $city->setState($this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\State')->find($rowData[0][1]));
            } else {
                $city->setState(null);
            }

            $this->getDoctrine()->getManager()->persist($city);
            $this->getDoctrine()->getManager()->flush();
        }
        die('done');
    }

    public function ambianceDocsImportAction($count)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');
        $rootPath = 'ambiance1/new1';
        $lowLimit = ($count - 1) * 50;
        $highLimit = $count * 50;
        $i = 0;
        $dirs = scandir($rootPath);
        foreach ($dirs as $dir) {
            $i++;
            if ($i < $lowLimit) {
                continue;
            }
            if ($i >= $highLimit) {
                die('Done from ' . $lowLimit . ' to ' . $highLimit);
            }
            if ($dir == '.' || $dir == '..') {
                continue;
            }
            $transactionId = $dir;
            $transaction = $em->getRepository('LocalsBestUserBundle:Transaction')->findOneBy(['ambianceTransId' => $transactionId]);
            if(is_null($transaction)){
                echo 'Problem transaction for: ' . $transactionId . '<br>';
                continue;
            }
            $object = $transaction->getClosing();
            $type = 'closing';
            if(is_null($object)) {
                $object = $transaction->getListing();
                $type = 'listing';
            }
            if(is_null($object)) {
                echo 'Problem transactions file for: ' . $transactionId . '<br>';
                continue;
            }
            if (count($object->getDocumentTypes()) > 0) {
                continue;
            }
            echo $dir . '<br>';
            $files = scandir($rootPath . '/' . $dir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $filePath = realpath($rootPath . '/' . $dir . '/' .$file);
                if(!is_file($filePath)){
                    echo 'Problem file: ' . $file . '<br>';
                    continue;
                }
                $fileInfo = pathinfo($filePath);
                $fileExtension = $fileInfo['extension'];
                $newFileName = '';
                foreach (explode('_', $file) as $key => $part) {
                    if ($key == 0 || $key == (count(explode('_', $file)) -1)) {
                        continue;
                    }
                    $newFileName .= $part . ' ';
                }
                $docTypeName = trim($newFileName);
                $newFileName = $newFileName . '.' . $fileExtension;
                $user = $transaction->getCreatedBy();
                $documentType = new EntityDocumentType($docTypeName, $user);
                $documentType->setIsRequired(true);
                $documentType->setApproved(true);
                $newFile = $user->getBusinesses()->first()->getId() . '/' . $transaction->getId() . '/' . $newFileName;
                $document = new Document();
                $document->setFileName($newFile);
                $document->setApproved(1);
                $document->setTransaction($transaction);
                $document->setOwner($user->getBusinesses()->first());
                $document->setCreatedBy($user);
                $sdk = $this->get('aws_sdk');
                $s3 = $sdk->createS3();
                $s3->putObject(array(
                    'Bucket' => 'ec-transactions',
                    'Key' => $newFile,
                    'Body' => fopen($filePath, 'r'),
                    'ACL' => 'public-read'
                ));
                $object->addDocumentTypeBy($documentType);
                $documentType->setDocument($document);
                if ($type == 'closing') {
                    $documentType->setClosing($object);
                } else {
                    $documentType->setListing($object);
                }
                $em->persist($object);
                $em->persist($documentType);
                $em->persist($document);
                $em->flush();
            }
        }
        die('done');
    }

    public function ambianceImportAction($count)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('AMBIANCE_OfficeTransClosingHistory_thru_12-19-15.xls');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');
        /** @var Business $business */
        $business = $em->getRepository('LocalsBestUserBundle:Business')->find(2);
        $role = $em->getRepository('LocalsBestUserBundle:Role')->find(7);
        $lowLimit = ($count - 1) * 10;
        $highLimit = $count * 10;
        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowDataSet = $sheet->rangeToArray('A' . $row . ':' . 'BC' . $row, NULL, TRUE, FALSE);
            $rowData = $rowDataSet[0];
            if ($row < $lowLimit) {
                continue;
            }
            if ($row >= $highLimit) {
                die('Done from ' . $lowLimit . ' to ' . $highLimit);
            }
            if ($rowData[1] == '') {
                break;
            }
            //  Insert row data array into your database of choice here
            $user = null;
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['portal_user_id' => $rowData[18]]);
            $fullName = explode(' ', $rowData[19]);
            $firstName = '';
            $lastName = $fullName[count($fullName)-1];
            foreach( $fullName as $key => $item) {
                if( $key == count($fullName)-1 ) {
                    break;
                }
                $firstName .= $item . ' ';
            }
            $firstName = trim($firstName);
            if (is_null($user)) {
                $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
            }
            if( is_null($user) ) {
                $user = new User();
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword('ambiance', '');
                $user->setPassword($password);
                $user->setUsername(str_replace(' ', '_', $rowData[19]));
                $user->setRole($role);
                $em->getRepository('LocalsBestUserBundle:User')->save($user);
                $business->addStaff($user);
                $em->getRepository('LocalsBestUserBundle:Business')->save($business);
                echo 'New Agent: ' . $user->getFirstName() . ' ' . $user->getLastName() .' (' . $user->getId() .') \n';
            }
            $client = new TransactionContact();
            $client->setContactName($rowData[21]);
            $client->setPhone($rowData[22]);
            $client->setEmail($rowData[23]);
            $agentContact = new TransactionContact();
            $agentContact->setContactName($rowData[19]);
            $agentContact->setPhone(null);
            $agentContact->setEmail(null);
            $officeContact = new TransactionContact();
            $officeContact->setContactName($business->getName());
            if($business->getContact()->getPhones()[0]) {
                $officeContact->setPhone($business->getContact()->getPhones()[0]->getNumber());
            } else {
                $officeContact->setPhone(null);
            }
            if($business->getContact()->getEmails()[0]) {
                $officeContact->setEmail($business->getContact()->getEmails()[0]->getEmail());
            } else {
                $officeContact->setEmail(null);
            }
            $createDate = date_create_from_format('n/j/Y g:i:s A', $rowData[28], new DateTimeZone('+0000'))->getTimestamp();
            $updateDate = date_create_from_format('n/j/Y g:i:s A', $rowData[54], new DateTimeZone('+0000'))->getTimestamp();
            $transaction = new Transaction();
            $transaction->setStatus($this->getRepository('LocalsBestUserBundle:Transaction')->getDefaultStatus())
                ->setCreatedBy($user)
                ->setCreated($createDate)
                ->setUpdated($updateDate);
            $transaction->setAmbianceTransId($rowData[2]);
            $transaction->setCreated($createDate);
            if( in_array($rowData[34], [13288897, 13263169, 13263929, 13286193, 13268373, 13286761, 13281715, 13285118, 13268507, 13112657, 13193562, 13270799, 13216471, 13276429, 13122267, 13251719, 13265326, 13197954, 13268787, 13265238, 13214969, 13178648, 13287236]) ) {
                $listing = new Listing();
                $listing->setSellerContact($client);
                $listing->set($client);
                $listing->setPortalTransactionId(0);
                $listing->setListingAgentContact($agentContact);
                $listing->setListingOfficeContact($officeContact);
                $listing->setRepresent($rowData[7] == 'Sale' ? 'Seller' : 'Landlord');
                $listing->setType($rowData[7] == 'Sale' ? 'Regular_Sale' : 'Lease');
                $listing->setStatus('Active');
                if (!count($listing->getReferrals()->toArray())) {
                    $referral = new Referral();
                    $referral->setType('%');
                    $listing->getReferrals()->add($referral);
                }
                if (!count($listing->getTotalCommissions()->toArray())) {
                    $commission = new Commission();
                    $commission->setType('%');
                    $listing->getTotalCommissions()->add($commission);
                    $listing->getTotalCommissions()->add($commission);
                }
                if (!count($listing->getBuyerAgentCommissions()->toArray())) {
                    $commission = new Commission();
                    $commission->setType('%');
                    $listing->getBuyerAgentCommissions()->add($commission);
                    $listing->getBuyerAgentCommissions()->add($commission);
                }
                if (!count($listing->getListingContacts()->toArray())) {
                    $firstEventAlert = new EventAlert();
                    $firstEvent = new Event();
                    $firstEvent->addAlert($firstEventAlert)
                        ->setIsRequired(TRUE);
                    $listing->getListingContacts()->add($firstEvent);
                    $secondEventAlert = new EventAlert();
                    $secondEvent = new Event();
                    $secondEvent->addAlert($secondEventAlert)
                        ->setIsRequired(TRUE);
                    $listing->getListingContacts()->add($secondEvent);
                    $thirdEventAlert = new EventAlert();
                    $thirdEvent = new Event();
                    $thirdEvent->addAlert($thirdEventAlert)
                        ->setIsRequired(TRUE);
                    $listing->getListingContacts()->add($thirdEvent);
                    $fourthEventAlert = new EventAlert();
                    $fourthEvent = new Event();
                    $fourthEvent->addAlert($fourthEventAlert)
                        ->setIsRequired(TRUE);
                    $listing->getListingContacts()->add($fourthEvent);
                    $fifthEventAlert = new EventAlert();
                    $fifthEvent = new Event();
                    $fifthEvent->addAlert($fifthEventAlert)
                        ->setIsRequired(TRUE);
                    $listing->getListingContacts()->add($fifthEvent);
                    $sixthEventAlert = new EventAlert();
                    $sixthEvent = new Event();
                    $sixthEvent->addAlert($sixthEventAlert)
                        ->setIsRequired(TRUE);
                    $listing->getListingContacts()->add($sixthEvent);
                }
                $addressData = new Address();
                $addressData->setStreet($rowData[35]);
                $addressData->setCity($rowData[36]);
                $addressData->setState($rowData[37]);
                $addressData->setZip($rowData[38]);
                $propertyData = new Property();
                $propertyData->setAddress($addressData);
                $propertyData->setType('Single_Family_Home');
                $transactionPropertyData = new TransactionProperty();
                $transactionPropertyData->setProperty($propertyData);
                $transactionPropertyData->setMlsNumber($rowData[34] ?: null);
                $transactionPropertyData->setYearBuilt(null);
                $moneyBox = new ListingMoneyBox();
                $moneyBox->setContractPrice($rowData[30]);
                $listing->setMoneyBox($moneyBox);
                $closingdata = $listing;
                foreach ($closingdata->getListingContacts() as $event) {
                    $event->setListing($closingdata);
                    $event->setType('Listing');
                    $event->setStatus($this->getRepository('LocalsBestUserBundle:Event')->getDefaultStatus());
                    if($business != null) {
                        $event->setOwner($business);
                    }
                    if( isset($selectedAgent) && ($this->getUser()->getRole()->getLevel() < $selectedAgent->getRole()->getLevel()) ) {
                        $event->setCreatedBy($selectedAgent);
                    }else {
                        $event->setCreatedBy($this->getUser());
                    }
                    if(count($event->getAlerts()->toArray())) {
                        foreach ($event->getAlerts() as $alert) {
                            $alert->setEvent($event);
                            $this->getDoctrine()->getManager()->persist($alert);
                            $event->addAlert($alert);
                        }
                    }
                    $this->getDoctrine()->getManager()->persist($event);
                    $closingdata->addListingContact($event);
                }
                $closedDate = date_create_from_format('n/j/Y g:i:s A', $rowData[32], new DateTimeZone('+0000'));
                $clDateEvent = new Event();
                $clDateEvent->setTitle('Expiration Date');
                $clDateEvent->setTime($closedDate);
                $clDateEvent->setListing($closingdata);
                $clDateEvent->setType('Listing');
                $em->getRepository('LocalsBestUserBundle:Event')->save($clDateEvent);
                $transaction->setListing($closingdata)
                    ->setCategory('listing')
                    ->setTransactionStatus($closingdata->getStatus())
                    ->setTransactionProperty($transactionPropertyData);
            } else {
                $closing = new Closing();
                $closing->setBuyerContact($client);
                $closing->setPortalTransactionId(0);
                $closing->setBuyersAgentContact($agentContact);
                $closing->setBuyersOfficeContact($officeContact);
                $closing->setRepresent($rowData[7] == 'Sale' ? 'Buyer' : 'Tenant');
                $closing->setType($rowData[7] == 'Sale' ? 'Regular_Sale' : 'Lease');
                $closing->setStatus('Sold_Paid');
                if (!count($closing->getReferrals()->toArray())) {
                    $referral = new Referral();
                    $referral->setType('%');
                    $closing->getReferrals()->add($referral);
                }
                if (!count($closing->getTotalCommissions()->toArray())) {
                    $commission = new Commission();
                    $commission->setType('%');
                    $closing->getTotalCommissions()->add($commission);
                    $closing->getTotalCommissions()->add($commission);
                }
                if (!count($closing->getBuyerAgentCommissions()->toArray())) {
                    $commission = new Commission();
                    $commission->setType('%');
                    $closing->getBuyerAgentCommissions()->add($commission);
                    $closing->getBuyerAgentCommissions()->add($commission);
                }
                if (!count($closing->getClosingContacts()->toArray())) {
                    $firstEventAlert = new EventAlert();
                    $firstEvent = new Event();
                    $firstEvent->addAlert($firstEventAlert)
                        ->setIsRequired(TRUE);
                    $closing->getClosingContacts()->add($firstEvent);
                    $secondEventAlert = new EventAlert();
                    $secondEvent = new Event();
                    $secondEvent->addAlert($secondEventAlert)
                        ->setIsRequired(TRUE);
                    $closing->getClosingContacts()->add($secondEvent);
                    $thirdEventAlert = new EventAlert();
                    $thirdEvent = new Event();
                    $thirdEvent->addAlert($thirdEventAlert)
                        ->setIsRequired(TRUE);
                    $closing->getClosingContacts()->add($thirdEvent);
                    $fourthEventAlert = new EventAlert();
                    $fourthEvent = new Event();
                    $fourthEvent->addAlert($fourthEventAlert)
                        ->setIsRequired(TRUE);
                    $closing->getClosingContacts()->add($fourthEvent);
                    $fifthEventAlert = new EventAlert();
                    $fifthEvent = new Event();
                    $fifthEvent->addAlert($fifthEventAlert)
                        ->setIsRequired(TRUE);
                    $closing->getClosingContacts()->add($fifthEvent);
                    $sixthEventAlert = new EventAlert();
                    $sixthEvent = new Event();
                    $sixthEvent->addAlert($sixthEventAlert)
                        ->setIsRequired(TRUE);
                    $closing->getClosingContacts()->add($sixthEvent);
                }
                $addressData = new Address();
                $addressData->setStreet($rowData[35]);
                $addressData->setCity($rowData[36]);
                $addressData->setState($rowData[37]);
                $addressData->setZip($rowData[38]);
                $propertyData = new Property();
                $propertyData->setAddress($addressData);
                $propertyData->setType('Single_Family_Home');
                $transactionPropertyData = new TransactionProperty();
                $transactionPropertyData->setProperty($propertyData);
                $transactionPropertyData->setMlsNumber($rowData[34] ?: null);
                $transactionPropertyData->setYearBuilt(null);
                $closing->getMoneyBox()->setContractPrice($rowData[30]);
                $closingdata = $closing;
                foreach ($closingdata->getClosingContacts() as $event) {
                    $event->setClosing($closingdata);
                    $event->setType('Closing');
                    $event->setStatus($this->getRepository('LocalsBestUserBundle:Event')->getDefaultStatus());
                    if (isset($selectedAgent) && ($this->getUser()->getRole()->getLevel() < $selectedAgent->getRole()->getLevel())) {
                        $event->setCreatedBy($this->getUser($selectedAgent));
                    } else {
                        $event->setCreatedBy($this->getUser());
                    }
                    if ($business != null) {
                        $event->setOwner($business);
                    }
                    if (count($event->getAlerts()->toArray())) {
                        foreach ($event->getAlerts() as $alert) {
                            $alert->setEvent($event);
                            $this->getDoctrine()->getManager()->persist($alert);
                            $event->addAlert($alert);
                        }
                    }
                    $this->getDoctrine()->getManager()->persist($event);
                    $closingdata->addClosingContact($event);
                }
                $closedDate = date_create_from_format('n/j/Y g:i:s A', $rowData[32], new DateTimeZone('+0000'));
                $clDateEvent = new Event();
                $clDateEvent->setTitle('Closing Date');
                $clDateEvent->setTime($closedDate);
                $clDateEvent->setClosing($closingdata);
                $clDateEvent->setType('Closing');
                $em->getRepository('LocalsBestUserBundle:Event')->save($clDateEvent);
                $transaction->setClosing($closingdata)
                    ->setCategory('closing')
                    ->setTransactionStatus($closingdata->getStatus())
                    ->setTransactionProperty($transactionPropertyData);
            }
            $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\TransactionProperty')->save($transactionPropertyData);
            if (!is_null($closing)) {
                $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Closing')->save($closingdata);
            } else {
                $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Listing')->save($closingdata);
            }
            $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Transaction')->save($transaction);
        }
        die('done');
    }

    protected function docRules($creating, $represent, $propertyType, $status, $transactionType, $year)
    {
        $anyKey = 'Any';
        $business = $this->getRepository('LocalsBestUserBundle:Business')
            ->find($this->get('session')->get('current_business'));

        $query = 'SELECT DISTINCT docs.documentName FROM LocalsBest\UserBundle\Entity\DocRule docs WHERE ';
        $conditions = array();

        if($business) {
            $conditions[] = 'docs.business = ' . $business->getId();
        }
        if ($creating) {
            $conditions[] = 'docs.creating = '. "'" .$creating ."'";
        }

        if ($represent) {
            $conditions[] = 'docs.represent IN (' . "'" . implode("','", array($represent, $anyKey)) . "'" . ')';
        }

        if ($propertyType) {
            $propertyType = explode('_', $propertyType);
            $propertyType = implode(' ', $propertyType);
            $conditions[] = 'docs.propertyType IN (' . "'" . implode("','", array($propertyType, $anyKey)) . "'" . ')';
        }

        if ($status) {
            $status = explode('_', $status);
            $status = implode(' ', $status);
            $conditions[] = 'docs.status IN (' . "'" . implode("','", array($status, $anyKey)) . "'" . ')';
        }

        if ($transactionType) {
            $transactionType = explode('_', $transactionType);
            $transactionType = implode(' ', $transactionType);
            $conditions[] = 'docs.transactionType   IN (' . "'" . implode("','", array($transactionType, $anyKey))
                . "'" . ')';
        }

        if ($year) {
            $conditions[] = "( ( docs.yearBuiltBefore > " . $year . " AND docs.yearBuiltAfter < " . $year
                . " ) OR ( docs.yearBuiltBefore = 'Any' AND docs.yearBuiltAfter = 'Any' ) )";
        }

        $query .= implode(' AND ', $conditions);
        $query = $this->getDoctrine()->getManager()->createQuery($query);
        $docNames = $query->getResult();
        return $docNames;
    }

    protected function questions($creating, $represent, $propertyType, $status, $transactionType, $referral, $hoa, $loan)
    {
        $anyKey = 'Any';
        $business = $this->getRepository('LocalsBestUserBundle:Business')
            ->find($this->get('session')->get('current_business'));

        $query = 'SELECT DISTINCT q.documentName FROM LocalsBest\UserBundle\Entity\TransactionQuestion q WHERE ';

        $conditions = array();

        if($business) {
            $conditions[] = 'q.business = ' . $business->getId();
        }
        if ($creating) {
            $conditions[] = 'q.creating = '. "'" .$creating ."'";
        }

        if ($represent) {
            $conditions[] = 'q.represent IN (' . "'" . implode("','", array($represent,$anyKey)) . "'" . ')';
        }

        if ($propertyType) {
            $propertyType = explode('_', $propertyType);
            $propertyType = implode(' ', $propertyType);
            $conditions[] = 'q.propertyType IN (' . "'" . implode("','", array($propertyType,$anyKey)) . "'" . ')';
        }

        if ($status) {
            $status = explode('_', $status);
            $status = implode(' ', $status);
            $conditions[] = 'q.status IN (' . "'" . implode("','", array($status,$anyKey)) . "'" . ')';
        }

        if ($transactionType) {
            $transactionType = explode('_', $transactionType);
            $transactionType = implode(' ', $transactionType);
            $conditions[] = 'q.transactionType IN (' . "'" . implode("','", array($transactionType,$anyKey))
                . "'" . ')';
        }

        $conditions[] = 'q.referral IN (' . "'" . implode("','", array($referral,$anyKey)) . "'" . ')';
        $conditions[] = 'q.hoa IN (' . "'" . implode("','", array($hoa,$anyKey)) . "'" . ')';
        $conditions[] = 'q.loan IN (' . "'" . implode("','", array($loan,$anyKey)) . "'" . ')';

        $query .= implode(' AND ', $conditions);

        $query = $this->getDoctrine()->getManager()->createQuery($query);
        $docNames = $query->getResult();

        return $docNames;
    }

    /**
     * Actions for importing information - End
     */

    /**
     * Get Cities by name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ajaxGetCitiesAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $query = $request->query->get('q');

            $result = $this->getRepository('LocalsBest\UserBundle\Entity\City')->createQueryBuilder('c')
                ->where('c.name LIKE :query')
                ->setParameter('query', $query . '%')
                ->getQuery()
                ->getResult();

            $endResult = array('results' => array(), 'more' => false);

            if(count($result) > 0) {
                foreach ($result as $item) {
                    $element = new stdClass();
                    $element->id = $item->getId();
                    $element->text = $item->getName();
                    $endResult['results'][] =  $element;
                }
            }

            return new JsonResponse($endResult);
        }

        die('403');
    }

    /**
     * Check email address for existing
     *
     * @param string $emailValue
     *
     * @return JsonResponse
     */
    public function mailCheckAction($emailValue)
    {
        $em = $this->getDoctrine()->getManager();

        $exist = false;
        $emailCheck = $em->getRepository('LocalsBestUserBundle:Email')->findOneBy(['email' => $emailValue]);

        if ($emailCheck !== null) {
            $exist = true;
        }

        return new JsonResponse([
            'result' => $exist,
        ]);
    }

    /**
     * Custom Order functionality
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function customOrderAction(Request $request)
    {
        // Create Form Object
        $form = $this->createFormBuilder()
            ->add('name', Type\TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', Type\EmailType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('request', Type\TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('send', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Get data from form
                $data = $request->request->all()['form'];

                // Send Email to Admin
                $this->getMailMan()->customOrderMail($data);

                // Show success message to User and redirect to another page
                $this->addFlash('success', 'Your request was received successfully.');
                return $this->redirectToRoute('products');
            }
        }

        // Render page layout
        return $this->render('@LocalsBestUser/default/custom-order.html.twig', ['form' => $form->createView()]);
    }

    public function championXlsDownloadAction()
    {
        $business = $this->getBusiness();

        if ($business === null || $business->getId() != 179) {
            throw $this->createAccessDeniedException();
        }

        // ask the service for a excel object
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject
            ->getProperties()->setCreator("EasyCloses")
            ->setLastModifiedBy("EasyCloses")
            ->setTitle("Clients List")
        ;

        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository('LocalsBestUserBundle:User')
            ->getClientsByBusiness($this->getBusiness(), $this->getUser());

        $phpExcelObject
            ->setActiveSheetIndex(0)
            ->setCellValue('A1', 'First Name')
            ->setCellValue('B1', 'Last Name')
            ->setCellValue('C1', 'Business')
            ->setCellValue('D1', 'Address')
            ->setCellValue('E1', 'Phone')
            ->setCellValue('F1', 'Email')
            ->setCellValue('G1', 'Size of the Bin')
            ->setCellValue('H1', 'Frequency')
            ->setCellValue('I1', 'Dates that documents were produced')
            ->setCellValue('J1', 'Links to get PDFs')
        ;

        $i = 2;
        foreach ($clients as $client) {

            $businessTitle = '-';
            $businessAddress = '-';

            if ($client->getBusinesses()->contains($business)) {
                if ($business->getId() == 173) {
                    if ($client->getClientBusiness() !== null) {
                        $businessTitle = $client->getClientBusiness()->getName();
                        $businessAddress = $client->getClientBusiness()->getAddress()->getFull();
                    }
                } else {
                    foreach ($client->getProperties() as $property) {
                        if (strtolower($property->getFormat()) == 'business') {
                            $businessTitle = $property->getTitle();
                            $businessAddress = $property->getAddress()->getFull();
                            break;
                        }
                    }
                }
            }

            // Get user documents
            $userDocuments = $em->getRepository('LocalsBest\UserBundle\Entity\DocumentUser')
                ->findMyDocuments($this->getUser(), $client, 'user');

            $links = [];
            $linksDates = [];
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

            /** @var DocumentUser $document */
            foreach ($userDocuments as $document) {
                $linksDates[] = date('m.d.Y H:i', $document->getCreated());
                $links[] = $helper->asset($document, 'file');
            }

            $phpExcelObject
                ->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $client->getFirstName())
                ->setCellValue('B'.$i, $client->getLastName())
                ->setCellValue('C'.$i, $businessTitle)
                ->setCellValue('D'.$i, $businessAddress)
                ->setCellValue('E'.$i, $client->getPrimaryPhone()->getNumber())
                ->setCellValue('F'.$i, $client->getPrimaryEmail()->getEmail())
                ->setCellValue('G'.$i, $client->getChampionBinSize())
                ->setCellValue('H'.$i, $client->getChampionFrequency())
                ->setCellValue('I'.$i, implode(', ', $linksDates))
                ->setCellValue('J'.$i, implode(', ', $links))
            ;
            $i++;
        }

        $phpExcelObject
            ->getActiveSheet()
            ->setTitle('Clients')
        ;

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ClientsList.xlsx'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
