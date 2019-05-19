<?php

namespace LocalsBest\UserBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\UserBundle\Entity\DocumentType;
use LocalsBest\UserBundle\Entity\Event;
use LocalsBest\UserBundle\Entity\Job;
use LocalsBest\UserBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DatatableController extends Controller
{
    /**
     * Send JSON of transactions for Transaction Summary Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function transactionsLimitAction(Request $request)
    {
       
        // Set memory limit
        ini_set('memory_limit', '175M');

        // Get request params
        $params = $request->query->all();
        $searchField = false;

        // Build array with params for query
        if( isset($params['mls_number']) && $params['mls_number'] == '' ) {
            unset($params['mls_number']);
        } elseif( isset($params['mls_number']) && $params['mls_number'] != '' ) {
            $searchField = true;
        }

        if( isset($params['agent_first_name']) && $params['agent_first_name'] == '' ) {
            unset($params['agent_first_name']);
        } elseif( isset($params['agent_first_name']) && $params['agent_first_name'] != '' ) {
            $searchField = true;
        }

        if( isset($params['agent_last_name']) && $params['agent_last_name'] == '' ) {
            unset($params['agent_last_name']);
        } elseif( isset($params['agent_last_name']) && $params['agent_last_name'] != '' ) {
            $searchField = true;
        }

        if( isset($params['contact_name']) && $params['contact_name'] == '' ) {
            unset($params['contact_name']);
        } elseif( isset($params['contact_name']) && $params['contact_name'] != '' ) {
            $searchField = true;
        }

        if( isset($params['street_address']) && $params['street_address'] == '' ) {
            unset($params['street_address']);
        } elseif( isset($params['street_address']) && $params['street_address'] != '' ) {
            $searchField = true;
        }

        if( isset($params['city_address']) && $params['city_address'] == '' ) {
            unset($params['city_address']);
        } elseif( isset($params['city_address']) && $params['city_address'] != '' ) {
            $searchField = true;
        }

        if( isset($params['state_address']) && $params['state_address'] == '' ) {
            unset($params['state_address']);
        } elseif( isset($params['state_address']) && $params['state_address'] != '' ) {
            $searchField = true;
        }

        if( isset($params['zip_address']) && $params['zip_address'] == '' ) {
            unset($params['zip_address']);
        } elseif( isset($params['zip_address']) && $params['zip_address'] != '' ) {
            $searchField = true;
        }

        if( isset($params['from']) ) {
            if ($params['from'] == '') {
                unset($params['from']);
            } else {
                $params['from'] = date_create_from_format('m/d/Y H:i:s', $params['from'] . ' 00:00:00')->getTimestamp();
                $searchField = true;
            }
        }

        if( isset($params['to']) ) {
            if ($params['to'] == '') {
                unset($params['to']);
            } else {
                $params['to'] = date_create_from_format('m/d/Y H:i:s', $params['to'] . ' 23:59:59')->getTimestamp();
                $searchField = true;
            }
        }

        if( isset($params['sp_from']) ) {
            if ($params['sp_from'] == '') {
                unset($params['sp_from']);
            } else {
                $params['sp_from'] = date_create_from_format('m/d/Y H:i:s', $params['sp_from'] . ' 00:00:00');
                $searchField = true;
            }
        }

        if( isset($params['sp_to']) ) {
            if ($params['sp_to'] == '') {
                unset($params['sp_to']);
            } else {
                $params['sp_to'] = date_create_from_format('m/d/Y H:i:s', $params['sp_to'] . ' 23:59:59');
                $searchField = true;
            }
        }

        if (isset($params['docsStatus'])) {
            $searchField = true;
        }

        if (isset($params['status']) && count($params['status']) > 0) {
            $searchField = true;
        }

        if (!isset($params['status']) && $searchField == false) {
            $params['status'] = ['Active', 'Pending', 'Sold_not_Paid', 'Leased_not_Paid'];
            $params['default'] = true;
        }

        if ($params['search']['value'] != ''
            && $searchField == false
            && (
                !in_array($this->getBusiness()->getId(), [19, 54])
                || $this->get('localsbest.checker')->productBySlug('global-transaction-summary-search')
            )
        ) {
            unset($params['status']);
        }

        $em = $this->getDoctrine()->getManager();

        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');

        // build business staff
        $staff = array();
        $u = $this->getUser();
        if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $businesses = $this->getUser()->getBusinesses();
            foreach ($businesses as $business) {
                $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                    ->findOneBy(array('id' => $business->getOwner()->getId()));
                $staff[] = $user;
            }
        } else {
            if($this->getUser()->isDocumentApprover()){
                $u = $this->getBusiness()->getOwner();
            } else {
                $u = $this->getUser();
            }
        }

        // Enable Soft Delete, but disable it for User Entity
        $em->getFilters()->enable('softdeleteable')->disableForEntity('LocalsBest\UserBundle\Entity\User');
        $transactions = $em->getRepository('LocalsBestUserBundle:Transaction')->dataTableQuery($u, $params);

        // Put default values for DataTable response
        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $transactions->count(),
            'recordsFiltered' => $transactions->count(),
        ];
        // Disable Soft Delete
        $em->getFilters()->disable('softdeleteable');

        $haveChanges = false;

        // Convert transaction info to JSON format
        foreach($transactions as $item) {
            if (is_array($item)) {
                $item = $item[0];
            }
            $array = [];
            /** @var \LocalsBest\UserBundle\Entity\Transaction $item */
            if (!is_null($item->getClosing())) {
                $file = $item->getClosing();
                $events = $file->getClosingContacts();
                if($file->getRepresent() == 'Seller' || $file->getRepresent() == 'Landlord') {
                    if(is_null($file->getSellerContact()) && !is_null($item->getListing())) {
                        if(!is_null($file)) {
                            $contact = $item->getListing()->getSellerContact()
                                ? $item->getListing()->getSellerContact()->getContactName()
                                : '-';
                        } else {
                            $contact = '-';
                        }
                    } else {
                        $contact = !is_null($file->getSellerContact())
                            ? $file->getSellerContact()->getContactName()
                            : '-';
                    }
                } else {
                    $contact = $file->getBuyerContact() ? $file->getBuyerContact()->getContactName() : '-';
                }
            } else {
                $file = $item->getListing();
                $events = $file->getListingContacts();
                $contact = $file->getSellerContact() ? $file->getSellerContact()->getContactName() : '-';
            }

            if ($contact == '-' && !is_null($file)) {
                $con = $em->getRepository('LocalsBestUserBundle:TransactionContact')
                    ->findOneBy(['role' => $file->getRepresent(), 'transaction' => $item->getId()]);
                if(!is_null($con)) {
                    $contact = $con->getContactName();
                }
            }
            // Enable Soft Delete
            $em->getFilters()->enable('softdeleteable');

            if($item->getDocumentsStatus() == null) {
                $color = $this->getTransactionDocumentsStatus($item);
                $item->setDocumentsStatus($color);
                $haveChanges = true;
            } else {
                $color = $item->getDocumentsStatus();
            }
            // Enable Soft Delete
            $em->getFilters()->enable('softdeleteable');
            // Disable Soft Delete
            $em->getFilters()->disable('softdeleteable');

            $i = 0;

            $array[$i++] = '<span class="badge" style="color: ' . $color . '; background-color: '. $color . '"> 3 </span>';
            $array[$i++] = str_replace('_', ' ', $item->getTransactionStatus());

            $array[$i++] = '<a id="btn-' . $item->getId() . '" href="'
                . $this->generateUrl('transaction_view', ['id' => $item->getId()])
                . '">' . $item->getFullAddress() . '</a>';
            $array[$i++] = ucfirst($item->getCategory());

            if ($this->getUser()->getRole()->getLevel() != 7) {
                $array[$i++] = $item->getCreatedBy()->getFirstName() . ' ' . $item->getCreatedBy()->getLastName();
            }

            $array[$i++] = $file->getRepresent();

            if(count($events) > 0) {
                $j = $i;
                foreach ($events as $event) {
                    if (strtolower($event->getTitle()) == strtolower('Closing Date')) {
                        $array[$i++] = $event->getTime() == null ? '-' : $event->getTime()->format('m-d-Y');
                        break;
                    } elseif (strtolower($event->getTitle()) == strtolower('Expiration Date')) {
                        $array[$i++] = $event->getTime() == null ? '-' : $event->getTime()->format('m-d-Y');
                        break;
                    }
                }
                if($j == $i) {
                    $array[$i++] = '-';
                }
            } else {
                $array[$i++] = '';
            }

            $array[$i++] = $file->getMoneyBox()->getContractPrice();
            $array[$i++] = $contact;
            $array[$i++] = $item->getTransactionProperty()->getMlsNumber() ?: '-';

            $array[$i++] = date("m-d-Y H:i", $item->getUpdated());
            $array[$i++] = date("m-d-Y H:i", $item->getCreated());
            $array[$i] = $this->render(
                '@LocalsBestUser/transaction/_actionButton.html.twig',
                ['transaction' => $item, 'file' => $file]
            )->getContent();

            $result['data'][] = $array;



            /* issue_3495 on redmine */

             $transactionAdress   = explode(',',$item->getFullAddress());

             $dealSheet   =[];

             
               $dealSheet['Property_street_address         '] =                                          str_replace(',',' ',$transactionAdress[0] ) ;
               $dealSheet['Property_City                   '] =                                          str_replace(',',' ',$transactionAdress[1] ) ;
               $dealSheet['Property_state                  '] =                                          str_replace(',',' ',$transactionAdress[2] ) ;
               $dealSheet['Property_zip                    '] =                                          str_replace(',',' ',$transactionAdress[3]  );
               $dealSheet['Property_Type                   '] =                                           str_replace(',',' ',$item->getTransactionProperty()->getProperty()->getType()) ;
               $dealSheet['Property_Year_Built             '] =                                           str_replace(',',' ',$item->getTransactionProperty()->getYearBuilt()) ;
              
               $dealSheet['Listing_Client_Type              '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getRepresent():'') ;
               $dealSheet['Listing_Transaction_Type         '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getType():'') ;
             
               $dealSheet['Listing_Seller_1_Full_Name       '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getSellerContact()?$item->getListing()->getSellerContact()->getContactName():'':'') ;
               $dealSheet['Listing_Seller_1_Telephone_Number'] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getSellerContact()?$item->getListing()->getSellerContact()->getPhone():'':'') ;
               $dealSheet['Listing_Seller_1_Email_Address   '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getSellerContact()?$item->getListing()->getSellerContact()->getEmail():'':'') ;
               if($item->getListing()){
                   if($item->getListing()->getSellerContact()){
               $dealSheet['Listing_Seller_1_Street_Address  '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact()->getAddress()?$item->getListing()->getSellerContact()->getAddress()->getStreet():'') ;
               $dealSheet['Listing_Seller_1_City            '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact()->getAddress()?$item->getListing()->getSellerContact()->getAddress()->getCity():'') ;
               $dealSheet['Listing_Seller_1_State           '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact()->getAddress()?$item->getListing()->getSellerContact()->getAddress()->getState():'') ;
               $dealSheet['Listing_Seller_1_Zip             '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact()->getAddress()?$item->getListing()->getSellerContact()->getAddress()->getZip():'') ;
                          }
               }

            $dealSheet['Listing_Seller_2_Full_Name       '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getContactName():'':'') ;
            $dealSheet['Listing_Seller_2_Telephone_Number'] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getPhone():'':'') ;
            $dealSheet['Listing_Seller_2_Email_Address   '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getEmail():'':'') ;
            if($item->getListing()){
                if($item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getAddress():false){
                    $dealSheet['Listing_Seller_2_Street_Address  '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getAddress()->getStreet():'') ;
                    $dealSheet['Listing_Seller_2_City            '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getAddress()->getCity():'') ;
                    $dealSheet['Listing_Seller_2_State           '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getAddress()->getState():'') ;
                    $dealSheet['Listing_Seller_2_Zip             '] =                                           str_replace(',',' ',$item->getListing()->getSellerContact2()?$item->getListing()->getSellerContact2()->getAddress()->getZip():'') ;
                }
            }


            $dealSheet['Listing_agent_Full_Name       '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getListingAgentContact()?$item->getListing()->getListingAgentContact()->getContactName():'':'') ;
            $dealSheet['Listing_agent_Phone_Number       '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getListingAgentContact()?$item->getListing()->getListingAgentContact()->getPhone():'':'') ;
               $dealSheet['Listing_agent_Email              '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getListingAgentContact()?$item->getListing()->getListingAgentContact()->getEmail():'':'') ;
               $dealSheet['Listing_agent_Company            '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getListingAgentContact()?$item->getListing()->getListingAgentContact()->getCompany():'':'') ;
               $dealSheet['Listing_Office_phone             '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getListingOfficeContact()?$item->getListing()->getListingOfficeContact()->getPhone():'':'') ;
               $dealSheet['Listing_Office_email             '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getListingOfficeContact()?$item->getListing()->getListingOfficeContact()->getEmail():'':'') ;
              
               $dealSheet['Seller_Attorney_Name             '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getTitleCompanyContact()?$item->getListing()->getTitleCompanyContact()->getContactName():'':'') ;
               $dealSheet['Seller_Attorney_Phone            '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getTitleCompanyContact()?$item->getListing()->getTitleCompanyContact()->getPhone():'':'') ;
               $dealSheet['Seller_Attorney_Email            '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getTitleCompanyContact()?$item->getListing()->getTitleCompanyContact()->getEmail():'':'') ;
               $dealSheet['Seller_Attorney_Company_Name     '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getTitleCompanyContact()?$item->getListing()->getTitleCompanyContact()->getCompany():'':'') ;
                if($dealSheet['Seller_Attorney_Name             ']==null){
                    $dealSheet['Seller_Attorney_Name             '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getTitleCompanyContact()?$item->getClosing()->getTitleCompanyContact()->getContactName():'':'');
                }
                if($dealSheet['Seller_Attorney_Phone            ']==null){
                    $dealSheet['Seller_Attorney_Phone            '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getTitleCompanyContact()?$item->getClosing()->getTitleCompanyContact()->getPhone():'':'');
                }
                if($dealSheet['Seller_Attorney_Email            ']==null){
                    $dealSheet['Seller_Attorney_Email            '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getTitleCompanyContact()?$item->getClosing()->getTitleCompanyContact()->getEmail():'':'');
                }
                if($dealSheet['Seller_Attorney_Company_Name     ']==null){
                    $dealSheet['Seller_Attorney_Company_Name     '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getTitleCompanyContact()?$item->getClosing()->getTitleCompanyContact()->getCompany():'':'');
                }
               $dealSheet['Listing_price                    '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getMoneyBox()?$item->getListing()->getMoneyBox()->getContractPrice():'':'') ;
               $dealSheet['Listing__total_Commission           '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getTotalCommissions()[0]?$item->getListing()->getTotalCommissions()[0]->getValue():'':'') ;
               $dealSheet['Listing__agent_Commission           '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getAgentCommissions()[0]?$item->getListing()->getAgentCommissions()[0]->getValue():'':'') ;
               $dealSheet['Listing__buyer_agent_Commission     '] =                                           str_replace(',',' ',$item->getListing()?$item->getListing()->getBuyerAgentCommissions()[0]?$item->getListing()->getBuyerAgentCommissions()[0]->getValue():'':'') ;
              
               if ($item->getListing() ){
                      foreach ($item->getListing()->getListingContacts() as $key => $event){
                          $dealSheet['Listing_event_'.$key.'_name'] =                                             str_replace(',',' ',$event->getTitle());
                          $dealSheet['Listing_event_'.$key.'_date'] =                                             $event->getTime()?$event->getTime()->format('d-m-Y'):'';
                     }
               }

          
               $dealSheet['closing_Client_Type             '] =                                                          str_replace(',',' ',$item->getClosing()?$item->getClosing()->getRepresent():'');
               $dealSheet['closing_Transaction_Type        '] =                                                          str_replace(',',' ',$item->getClosing()?$item->getClosing()->getType():'');
               $dealSheet['Closing_Buyer_1_Full_Name       '] =                                                          str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getContactName():'':'');
               $dealSheet['Closing_Buyer_1_Telephone_Number'] =                                                          str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getPhone():'':'');
               $dealSheet['Closing_Buyer_1_Email_Address   '] =                                                          str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getEmail():'':'');

                $dealSheet['Closing_Buyer_1_Street_Address'] =                                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getAddress()?$item->getClosing()->getBuyerContact()->getAddress()->getStreet():'':'':'');
                $dealSheet['Closing_Buyer_1_City          '] =                                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getAddress()?$item->getClosing()->getBuyerContact()->getAddress()->getCity():'':'':'');
                $dealSheet['Closing_Buyer_1_State         '] =                                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getAddress()?$item->getClosing()->getBuyerContact()->getAddress()->getState():'':'':'');
                $dealSheet['Closing_Buyer_1_Zip           '] =                                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact()?$item->getClosing()->getBuyerContact()->getAddress()?$item->getClosing()->getBuyerContact()->getAddress()->getZip():'':'':'');

                

                $dealSheet['Closing_Buyer_2_Full_Name       '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getContactName():'':'') ;
                $dealSheet['Closing_Buyer_2_Telephone_Number'] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getPhone():'':'') ;
                $dealSheet['Closing_Buyer_2_Email_Address   '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getEmail():'':'') ;

                
                $dealSheet['Closing_Buyer_2_Street_Address  '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getAddress()->getStreet():'':'') ;
                $dealSheet['Closing_Buyer_2_City            '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getAddress()->getCity():'':'') ;
                $dealSheet['Closing_Buyer_2_State           '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getAddress()->getState():'':'') ;
                $dealSheet['Closing_Buyer_2_Zip             '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerContact2()?$item->getClosing()->getBuyerContact2()->getAddress()->getZip():'':'') ;


                $dealSheet['Closing_Buyers_Agent_Name   '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersAgentContact()?$item->getClosing()->getBuyersAgentContact()->getContactName():'':'');
                $dealSheet['Closing_Buyers_Agent_Phone_Number   '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersAgentContact()?$item->getClosing()->getBuyersAgentContact()->getPhone():'':'');
                $dealSheet['Closing_Buyers_Agent_Email          '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersAgentContact()?$item->getClosing()->getBuyersAgentContact()->getEmail():'':'');
                $dealSheet['Closing_Buyers_Agent_Company        '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersAgentContact()?$item->getClosing()->getBuyersAgentContact()->getCompany():'':'');
                $dealSheet['Closing_Buyer_Office_Phone_Number   '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersOfficeContact()?$item->getClosing()->getBuyersOfficeContact()->getPhone():'':'');
                $dealSheet['Closing_Buyer_Office_Email          '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersOfficeContact()?$item->getClosing()->getBuyersOfficeContact()->getEmail():'':'');
                $dealSheet['Closing_Buyer_Office_Company        '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyersOfficeContact()?$item->getClosing()->getBuyersOfficeContact()->getCompany():'':'');

                $dealSheet['Closing_Buyer_Attorney_Name '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getSellerattorneyCompanyContact()?$item->getClosing()->getSellerattorneyCompanyContact()->getContactName():'':'');
                $dealSheet['Closing_Buyer_Attorney_Phone_Number '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getSellerattorneyCompanyContact()?$item->getClosing()->getSellerattorneyCompanyContact()->getPhone():'':'');
                $dealSheet['Closing_Buyer_Attorney_Email        '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getSellerattorneyCompanyContact()?$item->getClosing()->getSellerattorneyCompanyContact()->getEmail():'':'');
                $dealSheet['Closing_Buyer_Attorney_Company_Name '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getSellerattorneyCompanyContact()?$item->getClosing()->getSellerattorneyCompanyContact()->getCompany():'':'');
                
                $dealSheet['Closing_Contract_Price              '] =                                                     str_replace(',',' ',$item->getClosing()?$item->getClosing()->getMoneyBox()?$item->getClosing()->getMoneyBox()->getContractPrice():'':'');

                $dealSheet['Closing__payment_type                   '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getMoneyBox()?$item->getClosing()->getMoneyBox()->getPaymentType():'':'') ;
                $dealSheet['Closing__agent_Commission_advanced           '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()?$item->getClosing()->getAdvancedCommissionBox():'':'') ;
                $dealSheet['Closing__buyer_agent_Commission     '] =                                           str_replace(',',' ',$item->getClosing()?$item->getClosing()->getBuyerAgentCommissions()[0]?$item->getClosing()->getBuyerAgentCommissions()[0]->getValue():'':'') ;
            
            if ( $item->getClosing() ){
                foreach ($item->getClosing()->getClosingContacts() as $key=>$event) {
                $dealSheet['closing_event'.$key.'_name']=                                                     str_replace(',',' ',$event->getTitle());
                $dealSheet['closing_event'.$key.'_date']=                                                     str_replace(',',' ',$event->getTime()?$event->getTime()->format('d-m-Y'):'');
                }
              }
            $result['dealSheet'][]=$dealSheet;
              /* issue_3495 on redmine */

        }

        if($haveChanges == true) {
            // Save Changes
            $em->flush();
        }
        // Return JSON
        return new Response(json_encode($result));
    }

    /**
     * Send JSON of transactions for Firm Summary Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function firmLimitAction(Request $request)
    {
        // Limit memory
        ini_set('memory_limit', '175M');
        // Get request params
        $params = $request->query->all();

        // prepare array of params for query
        if( isset($params['from']) ) {
            if ($params['from'] == '') {
                unset($params['from']);
            } else {
                $params['from'] = date_create_from_format('m/d/Y H:i:s', $params['from'] . ' 00:00:00')->getTimestamp();
            }
        }

        if( isset($params['to']) ) {
            if ($params['to'] == '') {
                unset($params['to']);
            } else {
                $params['to'] = date_create_from_format('m/d/Y H:i:s', $params['to'] . ' 23:59:59')->getTimestamp();
            }
        }

        $em = $this->getDoctrine()->getManager();

        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');

        // Get Business Staff
        $staff = array();
        if ($this->getUser()->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $businesses = $this->getUser()->getBusinesses();
            foreach ($businesses as $business) {
                $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                    ->findOneBy(array('id' => $business->getOwner()->getId()));
                $staff[] = $user;
            }
        } else {
            if($this->getUser()->isDocumentApprover()){
                $u = $this->getBusiness()->getOwner();
            } else {
                $u = $this->getUser();
            }
        }

        // Enable Soft Delete Filter, but Disable it for User Entity
        $em->getFilters()->enable('softdeleteable')->disableForEntity('LocalsBest\UserBundle\Entity\User');

        // Get transactions using query
        $transactionsArray = $em->getRepository('LocalsBestUserBundle:Transaction')->dataTableFirmQuery($u, $params);
        $transactions = $transactionsArray[1];

        // Put default values for DataTable response
        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $transactionsArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $transactionsArray[0] : $transactionsArray[2],
        ];

        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');

        // Convert transaction info to JSON format
        foreach($transactions as $item) {
            if (is_array($item)) {
                $item = $item[0];
            }
            $array = [];
            $contactEntity = null;

            if (!is_null($item->getClosing())) {
                $file = $item->getClosing();
            } else {
                $file = $item->getListing();
            }

            foreach(explode('&', $file->getRepresent()) as $rl) {
                if (!is_null($item->getClosing())) {
                    $file = $item->getClosing();
                    if($rl == 'Seller' || $rl == 'Landlord') {
                        if(is_null($file->getSellerContact()) && !is_null($item->getListing())) {
                            $file = $item->getListing();
                            if(!is_null($file)) {
                                $contactEntity = $file->getSellerContact() ? $file->getSellerContact() : null;
                            }
                        } else {
                            $contactEntity = !is_null($file->getSellerContact()) ? $file->getSellerContact() : null;
                        }
                    } else {
                        $contactEntity = $file->getBuyerContact() ? $file->getBuyerContact() : null;
                    }
                } else {
                    $file = $item->getListing();
                    $contactEntity = $file->getSellerContact() ? $file->getSellerContact() : null;
                }

                if ($contactEntity === null && $file !== null) {
                    $contactEntity = $em->getRepository('LocalsBestUserBundle:TransactionContact')
                        ->findOneBy(['role' => $file->getRepresent(), 'transaction' => $item->getId()]);
                }

                if($contactEntity !== null) {
                    $cn = explode(' ', $contactEntity->getContactName());
                    $contact[1] = array_pop($cn);
                    $contact[0] = implode(' ', $cn);
                } else {
                    $contact = ['', ''];
                }

                $i = 0;

                $array[$i++] = date("m-d-Y", $item->getCreated());
                $array[$i++] = $rl;
                $array[$i++] = $contact[0];
                $array[$i++] = $contact[1];
                $array[$i++] = $contactEntity !== null ? $contactEntity->getPhone() : '';
                $array[$i++] = $contactEntity !== null ? $contactEntity->getEmail() : '';
                $array[$i] = $item->getFullAddress();

                $result['data'][] = $array;
            }
        }

        // Return JSON
        return new Response(json_encode($result));
    }

    /**
     * Send JSON of users for Users Summary Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function usersLimitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // get params from request
        $params = $request->query->all();
        // get current Business
        $business = $this->getBusiness();

        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');

        // Get Users by query
        $usersArray = $em->getRepository('LocalsBestUserBundle:User')->dataTableQuery($business, $params, $this->getUser());
        $users = $usersArray[1];

        // Put default values for DataTable response
        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $usersArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $usersArray[0] : $usersArray[2],
        ];

        // Convert user info to JSON format
        /** @var \LocalsBest\UserBundle\Entity\User $item */
        foreach($users as $item) {
            $array = [];
            $i = 0;

            $array[$i++] = !empty($item->getFilename())
                ? $this->get('localsbest.vich_uploader_custom')->getImage($item, 'file')
                : '<i class="icon-user-follow" style="font-size: 22px;"></i>' ;

            $checker = $this->container->get('localsbest.checker');

            if (
                $checker->forAddon('customers', $this->getUser())
                || $checker->forAddon('employee profiles', $this->getUser())
            ) {
                $array[$i++] = '<a href="'
                . $this->generateUrl('user_view', ['username' => $item->getUsername()]) . '" >'
                . $item->getFirstName() . '&nbsp;' . $item->getLastName() . '</a>';
            } else {
                $array[$i++] = $item->getFirstName() . '&nbsp;' . $item->getLastName();
            }

            $array[$i++] = $item->getPrimaryPhone() !== null ? $item->getPrimaryPhone()->getNumber() : '-';
            if($business->getId() == 179) {

                $property = $em->getRepository('LocalsBestUserBundle:Property')->findOneBy(['user' => $item, 'format' => 'Business']);

                $array[$i++] = $property !== null ? $property->getTitle() : '-';
                $array[$i++] = $property !== null ? $property->getAddress()->getFull() : '-';
                $array[$i++] = $item->getPrimaryEmail() !== null ? $item->getPrimaryEmail()->getEmail() : '-';
            } else {
                $array[$i++] = $item->getPrimaryEmail() !== null ? $item->getPrimaryEmail()->getEmail() : '-';
                $array[$i++] = ucfirst($item->getRole()->getName());
                $array[$i++] = date("m-d-Y H:i", $item->getUpdated());
            }
            $array[$i] = $this->render('@LocalsBestUser/user/_actionButton.html.twig', ['contact' => $item])->getContent();

            $result['data'][] = $array;
        }

        // Return JSON Response
        return new Response(json_encode($result));
    }

    public function clientsLimitAction(Request $request)
    {
        $params = $request->query->all();
        $em = $this->getDoctrine()->getManager();
        $business = $this->getBusiness();

        $clientsArray = $em->getRepository('LocalsBestUserBundle:User')->dataTableClientsQuery($business, $params, $this->getUser());

        $users = $clientsArray[1];

        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $clientsArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $clientsArray[0] : $clientsArray[2],
        ];

        /** @var \LocalsBest\UserBundle\Entity\User $item */
        foreach($users as $item) {
            $array = [];
            $i = 0;

            $array[$i++] = '<a href="' . $this->generateUrl('members_details', ['id' => $item->getId()]) . '" >' . $item->getFirstName() . '&nbsp;' . $item->getLastName() . '</a>';

            if ($this->getUser()->getId() == 2326) {
                $array[$i++] = $item->getAssignInventory();
            }

            if ($this->getUser()->getId() == 2338) {
                $array[$i++] = $item->getChampionBinSize();
                $array[$i++] = $item->getChampionFrequency();
            }

            $array[$i++] = $item->getPrimaryPhone() !== null ? $item->getPrimaryPhone()->getNumber() : '-';

            $businessTitle = '-';

            if ($item->getBusinesses()->contains($business)) {
                if ($business->getId() == 173) {
                    if ($item->getClientBusiness() !== null) {
                        $businessTitle = $item->getClientBusiness()->getName();
                    }
                } else {
                    foreach ($item->getProperties() as $property) {
                        if (strtolower($property->getFormat()) == 'business') {
                            $businessTitle = $property->getTitle();
                            break;
                        }
                    }
                }
            } else {
                $businessTitle = $item->getBusinesses()->first()->getName();
            }

            $array[$i++] = $businessTitle;

            $array[$i++] = date("m-d-Y H:i", $item->getUpdated());

            if($item->getBusinesses()->first() == $business) {
                $array[$i] = $this->render('@LocalsBestUser/user/_actionButton.html.twig', ['contact' => $item])->getContent();
            } else {
                $array[$i] = '-';
            }
            $result['data'][] = $array;
        }

        return new Response(json_encode($result));
    }

    /**
     * Send JSON of contacts for Contact Summary Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function contactsLimitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // get params from request
        $params = $request->query->all();

        // Get Contacts using query
        $contactsArray = $em->getRepository('LocalsBestUserBundle:AllContact')->dataTableQuery($this->getUser(), $params);
        $contacts = $contactsArray[1];

        // Put default values for DataTable response
        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $contactsArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $contactsArray[0] : $contactsArray[2],
        ];

        // Convert contacts info to JSON format
        /** @var \LocalsBest\UserBundle\Entity\AllContact $item */
        foreach($contacts as $el) {
            $item = $el[0];
            $array = [];
            $i = 0;
            $tags = [];

            /** @var \LocalsBest\CommonBundle\Entity\Tag $tag */
            foreach($item->getTags() as $tag){
                $tags[] = '<span class="label label-sm label-primary">' . $tag->getTag() . '</span>';
            }

            if($this->getUser()->getRole()->getLevel() < 7) {
                $array[$i++] = $el['firstName'] . ' ' . $el['lastName'];
            }
            $array[$i++] = '<a href="' .
                $this->generateUrl('contact_view', ['id' => $item->getId()]) . '" >'
                . $item->getFullName() . '</a>'
                . ( $item->getUser() !== null ? '<span class="label label-sm label-info">Member</span>' : '');
            $array[$i++] = $item->getNumber() !== null ? $item->getNumber() : '-';
            $array[$i++] = $item->getEmail() !== null ? $item->getEmail() : '-';
            $array[$i++] = ucfirst($item->getCategory());
            $array[$i++] = implode(' ', $tags);
            $array[$i++] = count($item->getNotes()) > 0 ? $this->limitNote($item->getNotes()->first()->getNote()) : '';
            $array[$i] = $this->render(
                '@LocalsBestUser/contact/_actionButton.html.twig',
                ['contact' => $item]
            )->getContent();

            $result['data'][] = $array;
        }
        // Return JSON Response
        return new Response(json_encode($result));
    }

    /**
     * Send JSON of Non Received Documents for NRD Summary Page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function NonReceivedDocumentsLimitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // get params from request
        $params = $request->query->all();
        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');

        // check is current user doc_approver
        if($this->getUser()->isDocumentApprover()){
            $u = $this->getBusiness()->getOwner();
        } else {
            $u = $this->getUser();
        }

        // Enable Soft Delete Filter, but disable it for User Entity
        $em->getFilters()->enable('softdeleteable')->disableForEntity('LocalsBest\UserBundle\Entity\User');

        // Get transactions using query
        $transactionsArray = $em->getRepository('LocalsBestUserBundle:Transaction')->dataTableNRDQuery($u, $params);
        $transactions = $transactionsArray[1];

        // Put default values for DataTable response
        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $transactionsArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $transactionsArray[0] : $transactionsArray[2],
        ];

        // Disable Soft Delete Filter
        $em->getFilters()->disable('softdeleteable');

        // Convert contacts info to JSON format
        /** @var \LocalsBest\UserBundle\Entity\Transaction $item */
        foreach($transactions as $item) {
            if (is_array($item)) {
                $item = $item[0];
            }
            $array = [];
            $i = 0;

            $array[$i++] = str_replace('_', ' ', $item->getTransactionStatus());
            $array[$i++] = '<a id="btn-' . $item->getId() . '" href="'
                . $this->generateUrl('transaction_view', ['id' => $item->getId()]) . '">'
                . $item->getTransactionProperty()->getProperty()->getAddress()->getCity() . '</a>';

            if ($this->getUser()->getRole()->getLevel() != 7) {
                $array[$i++] = '<a href="'
                    . $this->generateUrl('user_view', ['username' => $item->getCreatedBy()->getUsername()]) . '">'
                    . $item->getCreatedBy()->getFirstName() . ' ' . $item->getCreatedBy()->getLastName() . '</a>';
            }

            if($this->getUser()->getRole()->getLevel() == 7) {
                $user = $this->getBusiness()->getOwner();
            } else {
                $user = $this->getUser();
            }

            /** @var ArrayCollection $userImpDocs */
            $userImpDocs = $em->getRepository('LocalsBestUserBundle:ImportantDocument')->findBy(['user' => $user]);

            foreach ($userImpDocs as $key => $elem) {
                if($elem->getType() == 'listing') {
                    $array[$i++] = $this->docTypeStatus($item, $elem->getDocumentName());
                }
            }

            foreach ($userImpDocs as $key => $elem) {
                if($elem->getType() == 'closing') {
                    $array[$i++] = $this->docTypeStatus($item, $elem->getDocumentName(), true);
                }
            }

            foreach ($userImpDocs as $key => $elem) {
                if($elem->getType() == 'closing_sold_not_paid') {
                    $array[$i++] = $this->docTypeStatus($item, $elem->getDocumentName(), true);
                }
            }

            $buttonArray = $this->getRequiredLeft($item, $userImpDocs);
            $array[$i++] = $buttonArray['button'];
            $array[$i++] = $this->getNonRequiredLeft($item);

            if ($this->getUser()->getRole()->getLevel() != 7) {
                if(!empty($item)){
                $array[$i++] = $this->transactionCheck($item, 'listing');
                $array[$i++] = $this->transactionCheck($item, 'closing');
                $array[$i] = $buttonArray['needApprove'] == true
                    ? "<button type='button' class='btn btn-xs purple' href='"
                        . $this->generateUrl('ajax_documents', ['transactionId' => $item->getId()])
                        . "' data-transid='" . $item->getId()
                        . "' data-target='#documents' data-toggle='modal'>View</button>"
                    : '';
                }
            }

            // Return JSON Response
            $result['data'][] = $array;
        }

        return new Response(json_encode($result));
    }

    public function eventsLimitAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('softdeleteable');

        $params = $request->query->all();

        $em = $this->getDoctrine()->getManager();
        
        $eventArray = $em->getRepository('LocalsBestUserBundle:Event')->dataTableQuery($this->getUser(), $params);
       // dd($eventArray);
        $events = $eventArray[1];

        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $eventArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $eventArray[0] : $eventArray[2],
        ];

        /** @var \LocalsBest\UserBundle\Entity\Event $item */
        foreach($events as $item) {
            $array = [];
            $i = 0;

            if ($item->getStatus() === null || !in_array($item->getStatus()->getId(), [3, 5])) {
                $item->setStatus($em->getReference('LocalsBestCommonBundle:Status', 3));
                $em->flush();
            }

            $array[$i++] = $item->getColorCircle();
            $array[$i++] = $item->getStatus() !== null ? $item->getStatus()->getStatus() : '-';
            $array[$i++] = $this->getEventAddress($item);
            $array[$i++] = $item->getEndTime() !== null
                ? $item->getEndTime()->format('m/d/Y h:i a')
                : ($item->getTime() !== null ? $item->getTime()->format('m/d/Y h:i a') : '-');
            $array[$i++] = $item->getTitle();
            $array[$i++] = $item->getType() == 'Custom' ? $item->getCustom() : $item->getType();
            $array[$i++] = $this->getEventSharesWith($item);
            $array[$i++] = date('m/d/Y h:i a', $item->getCreated());

            $array[$i] = $this->renderView('@LocalsBestUser/event/_actionButton.html.twig', ['event' => $item]);

            $result['data'][] = $array;
        }

        return new JsonResponse($result);
    }

    public function jobsLimitAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('softdeleteable');

        $params = $request->query->all();

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $business = $user->getBusinesses()->first();


        if ($business->getId() == 173) {
            $jobArray = $em->getRepository('LocalsBestUserBundle:Job')->AsSignDataTableQuery($this->getUser(), $params);
        } else {
            $user_id = $request->query->get('user_id');

            if ($user_id !== null) {
                $anotherUser = $em->getRepository('LocalsBestUserBundle:User')->find($user_id);
            }

            $jobArray = $em->getRepository('LocalsBestUserBundle:Job')
                ->dataTableQuery(isset($anotherUser)?$anotherUser:$this->getUser(), $params);
        }
        $jobs = $jobArray[1];

        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $jobArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $jobArray[0] : $jobArray[2],
        ];

        /** @var \LocalsBest\UserBundle\Entity\Job $item */
        foreach($jobs as $item) {
            $array = [];
            $i = 0;

            $array[$i++] = $this->getJobColor($item);
            $array[$i++] = $item->getJobStatus();

            if ($business->getId() == 173) {
                $array[$i++] = '<a href="'
                    . $this->generateUrl('job_view', ['id' => $item->getId()]) . '">'
                    . $item->getOrderType() . '</a>';
                $array[$i++] = $item->getClient() !== null
                    ? $item->getClient()->getContactName()
                    : '<span class="muted">- No Client -</span>';
                $array[$i++] = $this->getVendorOBusiness($item);
                $array[$i++] = $item->getFullAddress();

                $array[$i] = '';

                if (
                    in_array($user->getRole()->getRole(), ['ROLE_ADMIN', 'ROLE_CUSTOMER_SERVIC'])
                    || $user->getRole()->getLevel() == 4
                ) {
                    $notes = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
                        ->findNotesForAdmin($user, 'LocalsBestUserBundle:Job', $item);
                } else {
                    $notes = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
                        ->findMyNotes($user, 'LocalsBestUserBundle:Job', $item);
                }


                foreach ($notes as $note) {
                    $array[$i] .= $note->getNote()."<br>";
                }
                $i++;
                $array[$i++] = date('m/d/Y H:i', $item->getCreated());
            } else {
                $array[$i++] = $item->getDueDate() !== null ? $item->getDueDate()->format('m-d-Y') : '-';
                $array[$i++] = '<a href="'
                    . $this->generateUrl('job_view', ['id' => $item->getId()]) . '">'
                    . $item->getOrderType() . '</a>';
                $array[$i++] = $item->getFullAddress();
                $array[$i++] = $this->getVendorOBusiness($item);
                $array[$i++] = $item->getClient() !== null
                    ? $item->getClient()->getContactName()
                    : '<span class="muted">- No Client -</span>';
                $array[$i++] = $item->getIndustryType() !== null
                    ? $item->getIndustryType()->getName()
                    : '<span class="muted">- None -</span>';
            }

            $array[$i++] = date('m/d/Y H:i', $item->getUpdated());
            $array[$i] = $this->renderView('@LocalsBestUser/job/_actionButton.html.twig', ['job' => $item]);

            $result['data'][] = $array;
        }

        return new JsonResponse($result);
    }

    private function getVendorOBusiness(Job $job)
    {
        if ($job->getVendor() !== null && $job->getVendor() == $this->getUser()) {
            if ($job->getCreatedBy()->getRole()->getLevel() == 8) {
                if ($job->getCreatedBy()->getClientBusiness() !== null) {
                    return $job->getCreatedBy()->getClientBusiness()->getName();
                } else {
                    return '-';
                }
            } else {
                return $job->getCreatedBy()->getBusinesses()->first()->getName();
            }
        }

        return $job->getVendor() !== null
            ? $job->getVendor()->getOwner()->getName()
            : '<span class="muted">- No Vendor -</span>';
    }
    
    public function shreddingDocumentsLimitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getFilters()->disable('softdeleteable');

        $params = $request->query->all();

        $business = $this->getBusiness();

        $user = $business->getOwner();

        $docArray = $em->getRepository('LocalsBestUserBundle:DocumentUser')->dataTableShreddingQuery($user, $params);

        $docs = $docArray[1];

        $result = [
            'draw' => (int)$params['draw'],
            'data' => [],
            'recordsTotal' => $docArray[2],
            'recordsFiltered' => !empty($params['search']['value']) ? $docArray[0] : $docArray[2],
        ];

        /** @var \LocalsBest\UserBundle\Entity\DocumentUser $item */
        foreach($docs as $item) {
            $array = [];
            $i = 0;

            $docUser = $item->getUser();
            $property = $docUser->getBusinessProperty();

            $array[$i++] = '<a href="'. $this->generateUrl('user_view', ['username' => $docUser->getUsername()]) . '">' . ($property !== null ? ($property->getTitle() == '' ? 'No Title' : $property->getTitle()) : '-') . '</a>';
            $array[$i++] = $property->getAddress()->getFull();
            $array[$i] = date("m/d/Y H:i", $item->getCreated()) ;

            $result['data'][] = $array;
        }

        return new JsonResponse($result);
    }
    /**
     * Generate table cell information
     *
     * @param \LocalsBest\UserBundle\Entity\Transaction $transaction
     * @param string $type
     *
     * @return string
     */
    private function transactionCheck($transaction, $type)
    {
        // Check transaction type
        if($type == 'listing') {
            if($transaction->getListing() === null) {
                return '';
            }
            // Get document Types
            $docTypes = $transaction->getListing()->getDocumentTypes();
        } else {
            if($transaction->getClosing() === null) {
                return '';
            }
            // Get document Types
            $docTypes = $transaction->getClosing()->getDocumentTypes();
        }

        // Get email scheduler for transaction
        $scheduler = $this->getDoctrine()->getRepository('LocalsBestUserBundle:NonReceivedDocsEmailScheduler')
            ->getScheduler($transaction, $type);

        if($scheduler !== null && $scheduler->getCounter() > 0) {
            // Get how many emails were sent
            $counterLayout = '<span class="badge badge-info">' . $scheduler->getCounter() . '</span>';
            if ($scheduler->getStatus() == true) {
                // if campaign status active put "stop" button
                $buttonLayout = '<a id="'. $type . '-' . $transaction->getId() . '" href="'
                    . $this->generateUrl(
                        'transactions_non_received_docs_toggle_scheduler_status',
                        ['id' => $scheduler->getId()]
                    )
                    . '" class="btn btn-sm btn-default"><i class="fa fa-stop"></i> Stop </a>';
            } else {
                // if campaign status not active put "re-start" button
                $buttonLayout = '<a id="'. $type . '-' . $transaction->getId() . '" href="'
                    . $this->generateUrl(
                        'transactions_non_received_docs_toggle_scheduler_status',
                        ['id' => $scheduler->getId()]
                    )
                    . '" class="btn btn-sm btn-default"><i class="fa fa-play"></i> Re - Start </a>';
            }
        } else {
            // put start campaign button
            $counterLayout = '';
            $buttonLayout = '<a id="'. $type . '-' . $transaction->getId() . '" href="'
                . $this->generateUrl(
                    'transaction_non_received_docs_emails',
                    ['transactionId' => $transaction->getId(), 'type' => $type]
                )
                . '"  data-target="#non-emails" data-toggle="modal" class="btn btn-sm btn-default">'
                . '<i class="fa fa-envelope"></i> Email </a>';
        }
        $email = false;

        /** @var \LocalsBest\UserBundle\Entity\DocumentType $docType */
        foreach ($docTypes as $docType) {
            if($docType->getIsRequired() == true && $docType->getDocument() === null && $docType->getDeleted() === null) {
                $email = true;
                break;
            }
        }

        if($email == true) {
            $result = $counterLayout;
            $result .= $buttonLayout;
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Calculate transaction documents color status
     *
     * @param \LocalsBest\UserBundle\Entity\Transaction $transaction
     * @param string $docTypeName
     * @param boolean $isClosing
     *
     * @return string
     */
    private function docTypeStatus($transaction, $docTypeName, $isClosing = false)
    {
        if($isClosing === false) {
            if($transaction->getListing() === null) {
                return '';
            }
            // Get Doc Types
            $docTypes = $transaction->getListing()->getDocumentTypes();
        } else {
            if($transaction->getClosing() === null) {
                return '';
            }
            // Get Doc Types
            $docTypes = $transaction->getClosing()->getDocumentTypes();
        }
        $color = "";

        // Calculate color status for transaction
        /** @var \LocalsBest\UserBundle\Entity\DocumentType $docType */
        foreach ($docTypes as $docType) {
            if($docType->getClearName() == $docTypeName && $docType->getDeleted() === null) {
                $color = "danger";

                if($docType->getDocument() !== null) {
                    $color = "warning";

                    if($docType->getApproved() == true) {
                        $color = "success";
                    }
                }
                break;
            }
        }

        return $color == '' ? '' : "<span id='doc-type-" . $docType->getId()
            . "' style='position: inherit; margin: 0 auto;' class='badge badge-"
            . $color . "'> &nbsp;&nbsp; </span>";
    }

    /**
     * Render view block for Required Docs of NRD
     *
     * @param $transaction
     * @param $userImpDocs
     *
     * @return array
     */
    private function getRequiredLeft($transaction, $userImpDocs)
    {
        $all = 0;
        $res = [];
        $resStatus = '';
        $docTypes = [];

        if($transaction->getListing() !== null) {
            $docTypes = array_merge($docTypes, $transaction->getListing()->getDocumentTypes()->toArray());
        }

        if($transaction->getClosing() !== null) {
            $docTypes = array_merge($docTypes, $transaction->getClosing()->getDocumentTypes()->toArray());
        }
        $needApprove = false;
        foreach ($docTypes as $elem) {

            if($elem->getDeleted() !== null) {
                continue;
            }

            if($elem->getDocument() !== null && $elem->getApproved() == false) {
                $needApprove = true;
            }

            if($elem->getIsRequired() == true) {
                $exist = false;
                foreach ($userImpDocs as $docType) {
                    if (in_array($docType->getType(), ['closing', 'closing_sold_not_paid'])
                        && $elem->getClosing() !== null) {

                        if ($docType->getDocumentName() == $elem->getClearName()) {
                            $exist = true;
                        }
                    }

                    if (in_array($docType->getType(), ['listing']) && $elem->getListing() !== null) {
                        if ($docType->getDocumentName() == $elem->getClearName()) {
                            $exist = true;
                        }
                    }
                }

                if($exist === false) {
                    if(
                        ($elem->getDocument() !== null && $elem->getApproved() == false)
                        || $elem->getDocument() === null
                    ) {
                        $all++;
                    }
                    $array = [];
                    $array['id'] = $elem->getId();
                    $array['name'] = $elem->getName();

                    $status = 'danger';

                    if($elem->getDocument() !== null && $elem->getApproved() == true) {
                        $status = 'success';
                    } elseif($elem->getDocument() !== null && $elem->getApproved() == false) {
                        $status = 'warning';
                        $needApprove = true;
                    }

                    $array['status'] = $status;
                    $resStatus = $this->getResultButtonStatus($array['status'], $resStatus);
                    $res[] = $array;
                }
            }
        }

        return ['button' => $this->render('@LocalsBestUser/transaction/_required-left.html.twig', [
                    'number' => $all,
                    'result' => $res,
                    'resStatus' => $resStatus,
                ])->getContent(),
            'needApprove' => $needApprove];
    }

    /**
     * Render view block for NonRequired Docs of NRD
     *
     * @param Transaction $transaction
     *
     * @return string
     */
    private function getNonRequiredLeft($transaction)
    {
        $all = 0;
        $res = [];
        $resStatus = '';
        $docTypes = [];

        if($transaction->getListing() !== null) {
            $docTypes = array_merge($docTypes, $transaction->getListing()->getDocumentTypes()->toArray());
        }

        if($transaction->getClosing() !== null) {
            $docTypes = array_merge($docTypes, $transaction->getClosing()->getDocumentTypes()->toArray());
        }

        foreach ($docTypes as $elem) {

            if($elem->getDeleted() !== null) {
                continue;
            }

            if($elem->getIsRequired() == false) {
                $all++;
                $array = [];
                $array['id'] = $elem->getId();
                $array['name'] = $elem->getName();
                $array['status'] = ($elem->getDocument() !== null && $elem->getApproved() == true)
                    ? 'success'
                    : (($elem->getDocument() !== null && $elem->getApproved() == false) ? 'warning' : 'default');

                $resStatus = $this->getResultButtonStatus($array['status'], $resStatus);
                $res[] = $array;
            }
        }

        return $this->render('@LocalsBestUser/transaction/_required-left.html.twig', [
            'number' => $all,
            'result' => $res,
            'resStatus' => $resStatus,
        ])->getContent();
    }

    /**
     * Calculate color status for button
     *
     * @param string $item
     * @param string $status
     *
     * @return null|string
     */
    private function getResultButtonStatus($item, $status)
    {
        $resStatus = null;
        if($item == 'success' && $status != 'warning' && $status != 'danger' && $status != 'default') {
            $resStatus = 'success';
        }

        if($item == 'warning' && $status != 'danger' && $status != 'default') {
            $resStatus = 'warning';
        }

        if($item == 'danger' || $item == 'default') {
            $resStatus = $item == 'default' ? 'grey' : 'danger';
        }

        return $resStatus !== null ? $resStatus : $status;
    }

    /**
     * Limit note message
     *
     * @param string $str
     *
     * @return string
     */
    private function limitNote($str)
    {
        if(strlen($str) > 100) {
            $pos = strpos($str, ' ', 100);

            $str = substr($str, 0, $pos) . '...';
        }
        return $str;
    }

    private function getJobColor (Job $job)
    {
        $color = '';

        if($job->getStatus()->getStatus() == 'new' && $job->getJobStatus() == 'New') {
            $color = '#d84a38';
        } elseif ($job->getStatus()->getStatus() == 'completed' && $job->getJobStatus() == 'New') {
            $color = '#ffb848';
        } elseif ($job->getJobStatus() == 'Open') {
            $color = '#ffb848';
        } elseif ($job->getJobStatus() == 'Closed') {
            $color = '#4d90fe';
        } elseif ($job->getJobStatus() == 'Updated') {
            $color = '#3cc051';
        }

        foreach ($job->getEvents() as $event) {
            if (strtolower($event->getTitle() == strtolower('Due Date'))) {
                if ($event->getTime()->format('U') < date('U') ) {
                    $color = '#800080';
                }
            }
        }

        return '<span class="badge" style="color: ' . $color . '; background-color: '. $color . '"> 3 </span>';;
    }

    private function getDocColor (DocumentType $docType)
    {
        $color = '';

        if($docType->getDocument() !== null) {
            if($docType->getApproved() == true) {
                $color = '#3cc051';
            } elseif($docType->getRejected() == true) {
                $color = '#000000';
            } else {
                $color = '#ffb848';
            }
        } else {
            if($docType->getStatus() == 'R') {
                $color = '#d84a38';
            } elseif($docType->getStatus() == '0') {
                $color = '#999999';
            }
        }

        return '<span class="badge" style="color: ' . $color . '; background-color: '. $color . '"> 3 </span>';

    }

    private function getEventAddress(Event $event)
    {
        $address = '-';
        return $address;
    }

    private function getEventSharesWith(Event $event)
    {
        $result = '';
        if ($event->getShares() !== null) {
            foreach ($event->getShares() as $share) {
                if ($share->getUser() != $this->getUser()) {
                    $result .= $this->renderView('@LocalsBestCommon/ui/user-avatar.html.twig', ['user' => $share->getUser()]);
                }
            }
        }

        return $result;
    }
}