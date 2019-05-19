<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Entity\Association;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\Document;
use LocalsBest\UserBundle\Entity\Email;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Log;
use LocalsBest\UserBundle\Entity\Phone;
use LocalsBest\UserBundle\Entity\State;
use LocalsBest\UserBundle\Entity\Transaction;
use LocalsBest\UserBundle\Entity\TransactionContact;
use LocalsBest\UserBundle\Form\DocumentType;
use LocalsBest\UserBundle\Entity\DocumentType as EntityDocumentType;
use Proxies\__CG__\LocalsBest\UserBundle\Entity\Contact;
use Doctrine\ORM\Query\ResultSetMapping;
use LocalsBest\UserBundle\Entity\User;

class ImportController extends Controller
{
    public function importAgentsAction($businessId)
    {
        set_time_limit(0);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('portal_users.xls');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $em = $this->getDoctrine()->getManager();
        $role = $em->getRepository('LocalsBestUserBundle:Role')->find(7);

        //  Loop through each row of the worksheet in turn
        for ($row = 1; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowDataSet = $sheet->rangeToArray('A' . $row . ':' . 'BR' . $row, NULL, TRUE, FALSE);
            $rowData = $rowDataSet[0];

            if ($rowData[0] != 'Locate Homes') {
                continue;
            }

            if($rowData[0] == 'Charles Rutenberg Realty Long Island') {
                $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['name' => 'Charles Rutenberg Realty Inc.']);
            } elseif($rowData[0] == 'Locate Homes') {
                $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['name' => 'Locatehomes.com']);
            } else {
                $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['name' => $rowData[0]]);
            }

            if(is_null($business)) {
                continue;
            }

            foreach($rowData as $key => $item) {
                if($item == 'NULL') {
                    $rowData[$key] = null;
                }
            }

            //  Insert row data array into your database of choice here
            $firstName = $rowData[13] ?: 'empty';
            $lastName = $rowData[14] ?: 'empty';

            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
            if (is_null($user)) {
                $user = new User();
                $user->setFirstName($firstName ?: 'empty');
                $user->setLastName($lastName ?: 'empty');

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword('admin', '');

                $user->setPassword($password);

                $un_count = count($em->getRepository('LocalsBestUserBundle:User')->findBy(['username' => $rowData[3]]));
                $user->setUsername($rowData[3] . ($un_count > 0 ? '_' . rand() : ''));

                $user->setRole($role);
                $user->setPortalUserId($rowData[2]);

                if($rowData[11] !== null && $rowData[11] !== '') {
                    $email = new Email($rowData[11]);
                    $user->getContact()->addEmail($email);
                }

                if($rowData[19] !== null && $rowData[19] !== '') {
                    $phone = new Phone();
                    $phone->setNumber($rowData[19]);
                    $phone->setType('M');
                    $user->getContact()->addPhone($phone);
                }

                if($rowData[63] != null){
                    $user->setAboutMe($rowData[63]);
                }

                $em->getRepository('LocalsBestUserBundle:User')->save($user);

                /*if($image = $this->get_data($rowData[6])) {
                    $newFile = 'logo/users/' . $user->getId() . '/' .  basename($rowData[6]);

                    $sdk = $this->get('aws_sdk');
                    $s3 = $sdk->createS3();

                    //TODO: change bucket fot live server "ec-transactions"
                    $result = $s3->putObject(array(
                        'Bucket' => 'dev-ec-users',
                        'Key' => $newFile,
                        'Body' => $image,
                        'ACL' => 'public-read'
                    ));

                    $user->setFile(new \Symfony\Component\HttpFoundation\File\File($image));
                    $user->setFileName($newFile);
                    $em->getRepository('LocalsBestUserBundle:User')->save($user);
                }*/

                echo 'New Agent: ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ') \n';
            } else {
                if (is_null($user->getPortalUserId()) || $user->getPortalUserId() == 0) {
                    $user->setPortalUserId($rowData[2]);
                    $em->persist($user);
                    $em->flush();
                }
            }
            if(count($user->getBusinesses()) == 0) {
                $user->addBusiness($business);
                $business->addStaff($user);
                $em->getRepository('LocalsBestUserBundle:User')->save($user);
                $em->getRepository('LocalsBestUserBundle:Business')->save($business);
            }
        }

        die('Agents');
    }

    // TODO: 'archive' => '1' shouldn't imported!!! and sort by transaction id
    public function importTransactionsAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_transactions.php');
        $em = $this->getDoctrine()->getManager();

        $i = 0;
        $batchSize = 100;

        foreach ($transactions as $item) {
            if ((int)$item['archive'] == 1) {
                continue;
            }
//            var_dump($item);die;
            //  Read a row of data into an array
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['portal_user_id' => $item['portal_id']]);

            if(is_null($user)){
                continue;
            }

            if (is_null($user->getBusinesses()) || $user->getBusinesses()->first() == false) {
                continue;
            }

//            if ($user->getBusinesses()->first()->getId() != 23) {
//                continue;
//            }

            $closing = $em->getRepository('LocalsBestUserBundle:Closing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);

            if (is_null($closing)) {
                $listing = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
                if (!is_null($listing)) {
                    /** @var \LocalsBest\UserBundle\Entity\Transaction $transaction */
                    $transaction = $listing->getTransaction();
                } else {
                    /** @var \LocalsBest\UserBundle\Entity\Transaction $transaction */
                    $transaction = null;
                }
            } else {
                /** @var \LocalsBest\UserBundle\Entity\Transaction $transaction */
                $transaction = $closing->getTransaction();
            }
//
//            if($transaction !== null) {
//                $transaction->setCreated(strtotime($item['creation_date']));
//                $transaction->setUpdated(strtotime($item['updated_time']));
//                $em->flush();
//            }
//
//            $i++;
//            if (($i % $batchSize) === 0) {
//                $em->clear(); // Detaches all objects from Doctrine!
//            }
//
//            continue;

            /*if( !is_null($em->getRepository('LocalsBestUserBundle:Transaction')->findOneBy(['portalTransactionId' => $item['transaction_id']])) ) {
                continue;
            }*/

            if($item['listing_id'] != '0') {
                $listing = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['listing_id']]);
                if(!is_null($listing)) {
                    $transaction = $listing->getTransaction();
                }
            }
            if(is_null($transaction)) {
                $transaction = new Transaction();
            }

            $transaction->setStatus($this->getRepository('LocalsBestUserBundle:Transaction')->getDefaultStatus())->setCreatedBy($user);
            if($item['type_transaction'] == 'Closing') {
                if(!is_null($transaction->getClosing())){
                    continue;
                }
                $closing = new \LocalsBest\UserBundle\Entity\Closing();
                $closing->setRepresent($item['i_represent']);
                $closing->setType(str_replace(' ', '_', $item['transaction_type']));
                $closing->setPortalTransactionId($item['transaction_id']);
                $closing->setStatus((ucfirst(str_replace(' ', '_', strtolower($item['trans_status'])))));

                if (!count($closing->getReferrals()->toArray())) {
                    $referral = new \LocalsBest\UserBundle\Entity\Referral();
                    $referral->setType('%');
                    $closing->getReferrals()->add($referral);
                }
                if (!count($closing->getTotalCommissions()->toArray())) {
                    $commission = new \LocalsBest\UserBundle\Entity\Commission();
                    $commission->setType('%');
                    $closing->getTotalCommissions()->add($commission);
                    $closing->getTotalCommissions()->add($commission);
                }
                if (!count($closing->getBuyerAgentCommissions()->toArray())) {
                    $commission = new \LocalsBest\UserBundle\Entity\Commission();
                    $commission->setType('%');
                    $closing->getBuyerAgentCommissions()->add($commission);
                    $closing->getBuyerAgentCommissions()->add($commission);
                }

                $addressData = new \LocalsBest\UserBundle\Entity\Address();
                $addressData->setStreet($item['street_address']);
                $addressData->setCity($item['city']);
                $addressData->setState($item['state']);
                $addressData->setZip($item['zip_code']);

                $propertyData = new \LocalsBest\UserBundle\Entity\Property();
                $propertyData->setAddress($addressData);
                $propertyData->setType(str_replace(' ', '_', $item['property_type'] == 'Condo' ? 'Condo/Co-op' : $item['property_type']));

                $transactionPropertyData = new \LocalsBest\UserBundle\Entity\TransactionProperty();
                $transactionPropertyData->setProperty($propertyData == 'Condo' ? 'Condo/Co-op' : $propertyData);
                $transactionPropertyData->setMlsNumber($item['mls'] ?: null);
                $transactionPropertyData->setYearBuilt($item['year_built']);
                $closing->getMoneyBox()->setContractPrice('$' . $item['transaction_price']);

                $closingdata = $closing;
                if($item['closing_date'] != '1969-12-31') {
                    $clDateEvent = new Event();
                    $clDateEvent->setTitle('Closing Date');
                    $clDateEvent->setTime(\DateTime::createFromFormat('Y-m-d', $item['closing_date']));
                    $clDateEvent->setClosing($closingdata);
                    $clDateEvent->setType('Closing');
//                    $em->getRepository('LocalsBestUserBundle:Event')->save($clDateEvent);
                    $em->persist($clDateEvent);
                }

                if($item['contract_date'] != '1969-12-31') {
                    $clDateEvent = new Event();
                    $clDateEvent->setTitle('Contract Signed');
                    $clDateEvent->setTime(\DateTime::createFromFormat('Y-m-d', $item['contract_date']));
                    $clDateEvent->setClosing($closingdata);
                    $clDateEvent->setType('Closing');
//                    $em->getRepository('LocalsBestUserBundle:Event')->save($clDateEvent);
                    $em->persist($clDateEvent);
                }
                $transactionData = $transaction->setClosing($closingdata)
                    ->setCategory('closing')
                    ->setTransactionStatus($closingdata->getStatus())
                    ->setTransactionProperty($transactionPropertyData);
            } else {
                if(!is_null($transaction->getListing())){
                    continue;
                }
                $listing = new \LocalsBest\UserBundle\Entity\Listing();
                $listing->setRepresent($item['i_represent']);
                $listing->setType(str_replace(' ', '_', $item['transaction_type']));
                $listing->setStatus($item['trans_status']);
                $listing->setPortalTransactionId($item['transaction_id']);
                if (!count($listing->getReferrals()->toArray())) {
                    $referral = new \LocalsBest\UserBundle\Entity\Referral();
                    $referral->setType('%');
                    $listing->getReferrals()->add($referral);
                }
                if (!count($listing->getTotalCommissions()->toArray())) {
                    $commission = new \LocalsBest\UserBundle\Entity\Commission();
                    $commission->setType('%');
                    $listing->getTotalCommissions()->add($commission);
                    $listing->getTotalCommissions()->add($commission);
                }
                if (!count($listing->getBuyerAgentCommissions()->toArray())) {
                    $commission = new \LocalsBest\UserBundle\Entity\Commission();
                    $commission->setType('%');
                    $listing->getBuyerAgentCommissions()->add($commission);
                    $listing->getBuyerAgentCommissions()->add($commission);
                }
                $addressData = new \LocalsBest\UserBundle\Entity\Address();
                $addressData->setStreet($item['street_address']);
                $addressData->setCity($item['city']);
                $addressData->setState($item['state']);
                $addressData->setZip($item['zip_code']);
                $propertyData = new \LocalsBest\UserBundle\Entity\Property();
                $propertyData->setAddress($addressData);
                $propertyData->setType(str_replace(' ', '_', $item['property_type'] == 'Condo' ? 'Condo/Co-op' : $item['property_type']));
                $transactionPropertyData = new \LocalsBest\UserBundle\Entity\TransactionProperty();
                $transactionPropertyData->setProperty($propertyData);
                $transactionPropertyData->setMlsNumber($item['mls'] ?: null);
                $transactionPropertyData->setYearBuilt($item['year_built']);
                $moneyBox = new \LocalsBest\UserBundle\Entity\ListingMoneyBox();
                $moneyBox->setContractPrice('$' . $item['transaction_price']);
                $listing->setMoneyBox($moneyBox);

                $closingdata = $listing;
                if($item['listing_date'] != '1969-12-31') {
                    $clDateEvent = new Event();
                    $clDateEvent->setTitle('Listing Date');
                    $clDateEvent->setTime(\DateTime::createFromFormat('Y-m-d', $item['listing_date']));
                    $clDateEvent->setListing($closingdata);
                    $clDateEvent->setType('Listing');
                    $em->persist($clDateEvent);
                }
                if($item['expiration_date'] != '1969-12-31') {
                    $clDateEvent = new Event();
                    $clDateEvent->setTitle('Expiration Date');
                    $clDateEvent->setTime(\DateTime::createFromFormat('Y-m-d', $item['expiration_date']));
                    $clDateEvent->setListing($closingdata);
                    $clDateEvent->setType('Listing');
                    $em->persist($clDateEvent);
                }
                $transactionData = $transaction->setListing($closingdata)
                    ->setCategory('listing')
                    ->setTransactionStatus($closingdata->getStatus())
                    ->setTransactionProperty($transactionPropertyData);
            }

            $em->persist($transactionPropertyData);
            if (!is_null($closing)) {
                $em->persist($closingdata);
            } else {
                $em->persist($closingdata);
            }
            $em->persist($transactionData);
            $i++;
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('Transactions');
    }

    public function importTransactionPartiesAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_transaction_roles.php');
        $em = $this->getDoctrine()->getManager();
        $i = 0;
        $batchSize = 100;
        foreach ($transaction_roles as $item) {
            $closing = $em->getRepository('LocalsBestUserBundle:Closing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
            if (is_null($closing)) {
                $listing = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
                if (!is_null($listing)) {
                    $transaction = $listing->getTransaction();
                } else {
                    $transaction = null;
                }
            } else {
                $transaction = $closing->getTransaction();
            }

            if(!$transaction) {
                continue;
            }

            if($item['transaction_type'] == 'Closing') {
                $contact = new TransactionContact();
                $contact->setEmail($item['email']);
                $contact->setPhone($item['primary_phone']);
                $contact->setContactName($item['contact_name']);
                $contact->setRole($item['transaction_role']);
                $contact->setCompany($item['business_name']);
                $contact->setCompanyEmail('');
                $contact->setCompanyPhone('');

                if(!$closing) {
                    continue;
                }
                switch ($item['transaction_role']) {
                    case 'Seller':
                        if (!is_null($closing->getSellerContact())) {
                            continue;
                        }
                        $closing->setSellerContact($contact);
                        break;
                    case 'Listing Agent':
                        if (!is_null($closing->getListingAgentContact())) {
                            continue;
                        }
                        $closing->setListingAgentContact($contact);
                        break;
                    case 'Listing Office':
                        if (!is_null($closing->getListingOfficeContact())) {
                            continue;
                        }
                        $closing->setListingOfficeContact($contact);
                        break;
                    case 'Buyer':
                        if (!is_null($closing->getBuyerContact())) {
                            continue;
                        }
                        $closing->setBuyerContact($contact);
                        break;
                    case 'Buyers Agent':
                        if (!is_null($closing->getBuyersAgentContact())) {
                            continue;
                        }
                        $closing->setBuyersAgentContact($contact);
                        break;
                    case 'Buyers Office':
                        if (!is_null($closing->getBuyersOfficeContact())) {
                            continue;
                        }
                        $closing->setBuyersOfficeContact($contact);
                        break;
                    case 'Title Company':
                        if (!is_null($closing->getTitleCompanyContact())) {
                            continue;
                        }
                        $closing->setTitleCompanyContact($contact);
                        break;
                    case 'Escrow Company':
                        if (!is_null($closing->getEscrowCompanyContact())) {
                            continue;
                        }
                        $closing->setEscrowCompanyContact($contact);
                        break;
                    case 'Home Inspector':
                        if (!is_null($closing->getHomeInspectorContact())) {
                            continue;
                        }
                        $closing->setHomeInspectorContact($contact);
                        break;
                    case 'Home Insurance':
                        if (!is_null($closing->getHomeInsuranceContact())) {
                            continue;
                        }
                        $closing->setHomeInsuranceContact($contact);
                        break;
                    case 'Lender':
                        if (!is_null($closing->getLenderContact())) {
                            continue;
                        }
                        $closing->setLenderContact($contact);
                        break;
                    default:
                        $contact->setRole($item['transaction_role']);
                        $contact->setTransaction($transaction);
                        $closing->setTransactionContacts($contact);
                        break;
                }
                $em->persist($contact);
                $em->persist($closing);
            } else {
                $contact = new TransactionContact();
                $contact->setEmail($item['email']);
                $contact->setPhone($item['primary_phone']);
                $contact->setContactName($item['contact_name']);
                $contact->setRole($item['transaction_role']);
                $contact->setCompany($item['business_name']);
                $contact->setCompanyEmail('');
                $contact->setCompanyPhone('');

                switch ($item['transaction_role']) {
                    case 'Seller':
                        if (!is_null($listing->getSellerContact())) {
                            continue;
                        }
                        $listing->setSellerContact($contact);
                        break;
                    case 'Listing Agent':
                        if (!is_null($listing->getListingAgentContact())) {
                            continue;
                        }
                        $listing->setListingAgentContact($contact);
                        break;
                    case 'Listing Office':
                        if (!is_null($listing->getListingOfficeContact())) {
                            continue;
                        }
                        $listing->setListingOfficeContact($contact);
                        break;
                    case 'Title Company':
                        if (!is_null($listing->getTitleCompanyContact())) {
                            continue;
                        }
                        $listing->setTitleCompanyContact($contact);
                        break;
                    default:
                        $contact->setRole($item['transaction_role']);
                        $contact->setTransaction($transaction);
                        $listing->setTransactionContact($contact);
                        break;
                }
                $em->persist($contact);
                $em->persist($listing);
            }
            $i++;
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('done');
    }

    public function importTransactionsDocumentsAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_attachment.php');

        $em = $this->getDoctrine()->getManager();
        $i = 0;
        $batchSize = 100;
        foreach ($attachment as $item) {
            $object = $em->getRepository('LocalsBestUserBundle:Closing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
            $type = 'closing';

            if(is_null($object)){
                $object = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
                $type = 'listing';
            }

            if (is_null($object)) {
                continue;
            }
            $transaction = $object->getTransaction();

            $existDocType = $em->getRepository('LocalsBestUserBundle:DocumentType')->findOneBy(['name' => $item['document_type_name'], $type => $object->getId()]);
            $user = $transaction->getCreatedBy();

            if(!is_null($existDocType)) {
                $documentType = $existDocType;
            } else {
                $documentType = new EntityDocumentType($item['document_type_name'], $user);
                $documentType->setIsRequired($item['document_type_required'] == 'unrequired' ? false : true);
                $documentType->setApproved($item['document_type_approve_status'] == 'Approved' ? true : false);
            }

            $newFile = $user->getBusinesses()->first()->getId() . '/' . $transaction->getId() . '/' . str_replace('/', '_', $item['document_name']);
            $oldFile = "TransactionFiles/" . $item['transaction_id'] . "/" . $item['document_name'];

            if($item['document_name'] == 'Closing Extension +FHA Amandatory Clause+Und Doc.pdf') {
                //https://s3.amazonaws.com/Localsbest/TransactionFiles/1250842/Closing+Extension+%2BFHA+Amandatory+Clause%2BUnd+Doc.pdf?X-Amz-Date=20151228T151551Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=3e2329598f16880f51dcccc0c0d92f8bbe81bfe0704424baa3d96c8553fb81f0&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D
                $oldFile = 'TransactionFiles/1250842/Closing+Extension+%2BFHA+Amandatory+Clause%2BUnd+Doc.pdf?X-Amz-Date=20151228T151551Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=3e2329598f16880f51dcccc0c0d92f8bbe81bfe0704424baa3d96c8553fb81f0&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D';
            }

            if($item['document_name'] == 'Title+Commitment+-+Loncar+to+Liu.pdf') {
                //https://s3.amazonaws.com/Localsbest/TransactionFiles/1265821/Title%2BCommitment%2B-%2BLoncar%2Bto%2BLiu.pdf?X-Amz-Date=20151228T152413Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=d2150f22d26d9e97db2c15cff7d186d52328f826e4e4a020f49b0e5e2c70e1a8&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D
                $oldFile = 'TransactionFiles/1265821/Title%2BCommitment%2B-%2BLoncar%2Bto%2BLiu.pdf?X-Amz-Date=20151228T152413Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=d2150f22d26d9e97db2c15cff7d186d52328f826e4e4a020f49b0e5e2c70e1a8&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D';
            }

            if($item['document_name'] == 'hud+settlement+statement+-+15006100tpf.pdf') {
                //https://s3.amazonaws.com/Localsbest/TransactionFiles/1266304/hud%2Bsettlement%2Bstatement%2B-%2B15006100tpf.pdf?X-Amz-Date=20151228T152748Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=95d92d4556317143d7e9ec507b40b501da1d3fd54960b7287bffb736b7c9d15d&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D
                $oldFile = 'TransactionFiles/1266304/hud%2Bsettlement%2Bstatement%2B-%2B15006100tpf.pdf?X-Amz-Date=20151228T152748Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=95d92d4556317143d7e9ec507b40b501da1d3fd54960b7287bffb736b7c9d15d&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D';
            }

            if ($item['transaction_id'] == '1267590' && $item['document_name'] == 'Property Detail Report.pdf') {
                //https://s3.amazonaws.com/Localsbest/TransactionFiles/1267590/Property+Detail%C2%A0Report.pdf?X-Amz-Date=20151228T152915Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=c51aea44dd503b78c9f13720bcb20197abab37927885ab4d9ad3207cff928960&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D
                $oldFile = 'TransactionFiles/1267590/Property+Detail%C2%A0Report.pdf?X-Amz-Date=20151228T152915Z&X-Amz-Expires=300&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Signature=c51aea44dd503b78c9f13720bcb20197abab37927885ab4d9ad3207cff928960&X-Amz-Credential=ASIAJTMR623ZMEDJMYMQ/20151228/us-east-1/s3/aws4_request&X-Amz-SignedHeaders=Host&x-amz-security-token=AQoDYXdzEJD//////////wEagAOQwxyuiyjMYKt0fLg3KYBbEs%2BmHymDEg94tDukOksviJTWvo1CyfddgRnQp4PrqkHqP14XrAQYc8fBLd9%2B366WKoCEPZAVz7I53SbaFqBReRHLSyCCsX6G38NK252NPzA6maMswEtWOkTkexmV1gIwLC4CfKpp7V3VuOR2HpRMPCte/W/EfgTgcN6ccjF9mD/55qFNeKdumoHC1wfzGyz6HJMrGXOCb8pnsPegEIPgo5c759Lwr5M/Iox/xvvdGH68vMKlC4QS/3iYfW0Rl/updimaJiPIvjWdo7y9uj%2BDITcMpicKW4zZX9MhZ3nYr6JPIqOAO17gcDguAP2bspHcVOGUR4B0qOHY00i3AZKYIq6NGUidByAe5psQ/Yp0TZv5wrRhQypUU%2B0D0zhN9n9MNVxRW3MC6IArmsNwKizbJh5UTnXLgmgizDMwGfIMIJtoeFIMnJFwKX7MqjvWGfW5Vqx3ijDaZSK0GvhPC50zH7QMVRlE1g4/4GIz2ZN6SPcgwaCFtAU%3D';
            }

            if (!is_null($documentType->getDocument())) {
                echo 'doc type: ' . $item['document_type_name'] . ' exist (transaction id ' . $item['transaction_id'] . ') and attached to '. $type . ' with id ' . $object->getId() . '<br>';
                continue;
            }

            $document = new Document();
            $document->setFileName($newFile);
            $document->setTransaction($transaction);
            $document->setOwner($user->getBusinesses()->first());
            $document->setCreatedBy($user);

            $sdk = $this->get('aws_sdk');
            $s3 = $sdk->createS3();

            $info = $s3->doesObjectExist('Localsbest', $oldFile);

            if (!$info){
            } else {
                $s3->copyObject(array(
                    'Bucket'     => $this->get('transactions_bucket'),
                    'Key'        => $newFile,
                    'CopySource' => "Localsbest/{$oldFile}",
                    'ACL'        => 'public-read'
                ));
            }

            $object->addDocumentTypeBy($documentType);
            if ($info) {
                $documentType->setDocument($document);
            }
            if ($type == 'closing') {
                $documentType->setClosing($object);
            } else {
                $documentType->setListing($object);
            }

            $em->persist($object);
            $em->persist($documentType);
            if ($info) {
                $em->persist($document);
            }
            $i++;
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('import docs done');
    }

    public function importTransactionsDocTypesAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_addon_doc_vault.php');
        $i = 0;
        $batchSize = 100;
        $em = $this->getDoctrine()->getManager();

        foreach ($addon_doc_vault as $item) {
            $object = $em->getRepository('LocalsBestUserBundle:Closing')->findOneBy(['portalTransactionId' => $item['closing_id']]);
            $type = 'closing';
            if(is_null($object)){
                $object = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['closing_id']]);
                $type = 'listing';
            }

            if (is_null($object)) {
                continue;
            }
            $transaction = $object->getTransaction();
            $existDocType = $em->getRepository('LocalsBestUserBundle:DocumentType')->findOneBy(['name' => $item['attach_name'], $type => $object->getId()]);

            if(!is_null($existDocType)) {
                echo 'doc type: ' . $item['attach_name'] . ' exist (transaction id ' . $item['closing_id'] . ') and attached to '. $type . ' with id ' . $object->getId() . '<br>';
                continue;
            }
            $user = $transaction->getCreatedBy();

            $documentType = new EntityDocumentType($item['attach_name'], $user);
            $documentType->setIsRequired($item['type'] == 'required' ? true : false);
            $documentType->setApproved(false);

            $object->addDocumentTypeBy($documentType);

            if ($type == 'closing') {
                $documentType->setClosing($object);
            } else {
                $documentType->setListing($object);
            }

            $em->persist($object);
            $em->persist($documentType);

            $i++;
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('Doc Types done');
    }

    public function importTransactionsNotesAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_closing_changes.php');
        $i = 0;
        $batchSize = 100;
        $em = $this->getDoctrine()->getManager();

        foreach ($closing_changes as $item) {
            $object = $em->getRepository('LocalsBestUserBundle:Closing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
            if(is_null($object)){
                $object = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['transaction_id']]);
            }
            if (is_null($object)) {
                continue;
            }
            $transaction = $object->getTransaction();
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['portal_user_id' => $item['portal_user_id']]);

            if(is_null($user)) {
                continue;
            }

            $note = new Note();
            $note->setStatus($em->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus());

            $note   ->setNote($item['closing_comments'])
                ->setPrivate(true)
                ->setImportant(false)
                ->setObjectType('LocalsBestUserBundle:Transaction')
                ->setUser($user)
                ->setCreatedBy($user);

            if($user->getBusinesses() != null && $user->getBusinesses()->first() != null) {
                $note->setOwner($user->getBusinesses()->first());
            }

            $note->setObjectId($transaction->getId());
            $transaction->addNote($note);

            $i++;
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('Notes done');
    }

    public function importTransactionsLogsAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_closing_operation.php');
        $em = $this->getDoctrine()->getManager();
        $i = 0;
        $batchSize = 100;
        foreach ($closing_operation as $item) {
            $object = $em->getRepository('LocalsBestUserBundle:Closing')->findOneBy(['portalTransactionId' => $item['closing_id']]);
            if(is_null($object)){
                $object = $em->getRepository('LocalsBestUserBundle:Listing')->findOneBy(['portalTransactionId' => $item['closing_id']]);
            }

            if (is_null($object)) {
                continue;
            }
            $transaction = $object->getTransaction();
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['portal_user_id' => $item['portal_user_id']]);
            if(is_null($user)) {
                continue;
            }

            $log = new Log();
            $date = new \DateTime($item['operation_time']);
            $log->setCreatedBy($user)
                ->setLog($item['operation_description'])
                ->setTransaction($transaction)
                ->setCreated($date->getTimestamp());

            $em->persist($log);
            $transaction->addLog($log);

            $i++;
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('done');
    }

    private function get_data($url)
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        if (!$html) {
            return false;
        }
        else{
            return $html;
        }
    }

    public function ambianceAgentsImportAction()
    {
        set_time_limit(0);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('AMBIANCE_OfficeAssocHistory_20151219T121747.xls');
        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $em = $this->getDoctrine()->getManager();
        $role = $em->getRepository('LocalsBestUserBundle:Role')->find(7);
        $business = $em->getRepository('LocalsBestUserBundle:Business')->find(2);

        $association = $em->getRepository('LocalsBestUserBundle:Association')->findOneBy(['title' => 'Ntreis']);

        if ( is_null($association) ) {
            $association = new Association();
            $association->setTitle('Ntreis');
            $em->persist($association);
        }

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $rowDataSet = $sheet->rangeToArray('A' . $row . ':' . 'AL' . $row, NULL, TRUE, FALSE);
            $rowData = $rowDataSet[0];

            foreach($rowData as $key => $item) {
                if($item == 'NULL') {
                    $rowData[$key] = null;
                }
            }

            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['portal_user_id' => $rowData['2']]);

            if ( !is_null($user) ) {
                continue;
            }

            $fullName = explode(' ', $rowData[3]);
            $firstName = '';
            $lastName = $fullName[count($fullName)-1];

            foreach( $fullName as $key => $item) {
                if( $key == count($fullName)-1 ) {
                    break;
                }
                $firstName .= $item . ' ';
            }

            $firstName = trim($firstName);
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);

            if (is_null($user)) {
                $user = new User();
            }

            $user->setFirstName($firstName ?: 'empty');
            $user->setLastName($lastName ?: 'empty');

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword('ambiance', '');

            $user->setPassword($password);

            if( is_null($user->getUsername()) || $user->getUsername() == '' ) {
                $un_count = count($em->getRepository('LocalsBestUserBundle:User')->findBy(['username' => $rowData[26]]));
                $user->setUsername($rowData[26] . ($un_count > 0 ? '_' . rand() : ''));
            }

            $user->setRole($role);
            $user->setPortalUserId($rowData[2]);
            $em->getRepository('LocalsBestUserBundle:User')->save($user);

            if($rowData[26] !== null && $rowData[26] !== '') {
                $email = new Email($rowData[26]);
                $user->getContact()->addEmail($email);
            }

            if($rowData[27] !== null && $rowData[27] !== '') {
                $email = new Email($rowData[27]);
                $user->getContact()->addEmail($email);
            }

            if($rowData[23] !== null && $rowData[23] !== '') {
                $phone = new Phone();
                $phone->setNumber($rowData[19]);
                $phone->setType('M');
                $user->getContact()->addPhone($phone);
            }

            if($rowData[15] !== null && $rowData[15] !== '') {
                $user->setAssociationId($rowData[15]);
                $user->addAssociation($association);
                if($rowData[16] !== null && $rowData[16] !== '') {
                    $licExpDate = date_create_from_format('n/j/Y g:i:s A', $rowData[16], new \DateTimeZone('+0000'));
                    $user->setLicenseExpirationDate($licExpDate);
                }
            }

            if($rowData[30] !== null && $rowData[30] !== '') {
                $joinedDate = date_create_from_format('n/j/Y g:i:s A', $rowData[30], new \DateTimeZone('+0000'));
                $user->setJoinedCompanyDate($joinedDate);
            }

            if($rowData[32] !== null && $rowData[32] !== '') {
                $terminatedDate = date_create_from_format('n/j/Y g:i:s A', $rowData[32], new \DateTimeZone('+0000'));
                $user->setJoinedCompanyDate($terminatedDate);
            }

            if($rowData[32] !== null && $rowData[32] !== '') {
                $terminatedDate = date_create_from_format('n/j/Y g:i:s A', $rowData[32], new \DateTimeZone('+0000'));
                $user->setDeleted($terminatedDate);
            }

            echo 'New Agent: ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ') \n';
            
            $em->persist($user);
            $em->flush();

            if(count($user->getBusinesses()) == 0) {
                $user->addBusiness($business);
                $business->addStaff($user);
                $em->getRepository('LocalsBestUserBundle:User')->save($user);
                $em->getRepository('LocalsBestUserBundle:Business')->save($business);
            }
        }
        die('Agents');
    }

    public function importContactsAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        include('LH_contacts.php');
        $em = $this->getDoctrine()->getManager();

        $i = 0;
        $batchSize = 100;

        foreach ($contacts as $item) {

            /** @var \LocalsBest\UserBundle\Entity\User $user */
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['portal_user_id' => $item['created_portal_user_id']]);

            if(is_null($user)){
                continue;
            }

            $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy([
                'createdBy' => $user,
                'firstName' => $item['contact_firstname'],
                'lastName' => $item['contact_lastname'],
                'email' => $item['contact_email'],
                'number' => $item['contact_primary_phone'],
            ]);

            if (is_null($contact)){
                $contact = new AllContact();
                $contact->setFirstName($item['contact_firstname']);
                $contact->setLastName(!is_null($item['contact_lastname']) ? $item['contact_lastname'] : '');
                $contact->setEmail($item['contact_email']);
                $contact->setNumber($item['contact_primary_phone']);
                $contact->setCreatedBy($user);

                $em->persist($contact);
                $em->flush();
            }

            if (!is_null($item['note_content'])) {
                $note = new Note();
                $note->setCreatedBy($user);
                $note->setNote($item['note_content']);
                $note->setCreated(strtotime($item['note_timestamp']));
                $note->setObjectType('LocalsBestUserBundle:AllContact');
                $note->setObjectId($contact->getId());
                $note->setPrivate(true);
                $contact->addNote($note);

                $em->persist($note);
                $em->flush();
            }

            $i++;
            if (($i % $batchSize) === 0) {
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush();
        $em->clear();
        die('Transactions');
    }
}