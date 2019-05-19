<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\OrderItem;
use LocalsBest\ShopBundle\Entity\Payment;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\UserOrder;
use LocalsBest\ShopBundle\Entity\UserPlugin;
use LocalsBest\SocialMediaBundle\Entity\BucketSchedulerSet;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LocalsBest\UTIL\SortHelper;

class DatatableController extends Controller {

    public function managerListAction(Request $request) {
	// Get request params
	$params = $request->query->all();

	$business = $this->getBusiness();

	if (isset($params['from'])) {
	    if ($params['from'] == '') {
		unset($params['from']);
	    } else {
		$params['from'] = date_create_from_format('m/d/Y H:i:s', $params['from'] . ' 00:00:00');
		$searchField = true;
	    }
	}

	if (isset($params['to'])) {
	    if ($params['to'] == '') {
		unset($params['to']);
	    } else {
		$params['to'] = date_create_from_format('m/d/Y H:i:s', $params['to'] . ' 23:59:59');
		$searchField = true;
	    }
	}
	$orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
		->findForBusiness($business, $params);
        
        $original_length= 0;
        foreach ($orders as $order) {
            foreach($order->getUser()->getBusinesses() as $buyer_business){
                $buyer_company_id= $buyer_business->getId();
            }
            foreach($this->getUser()->getBusinesses() as $current_user_business){
                $current_user_company = $current_user_business->getId();
            } 
            
            if($buyer_company_id!=$current_user_company && $current_user_company!=''&& $buyer_company_id!=''){
                continue;
            }
            $original_length++;
        }
        
	// Put default values for DataTable response
	$result = [
	    'draw' => (int) $params['draw'],
	    'data' => '',
	    'recordsTotal' => $original_length,
	    'recordsFiltered' => $original_length,
	];

	$charityAmount = 0;
	$managerAmount = 0;
	$employeeAmount = 0;
	$totalPrice = 0;
	$totalRebateAmount = 0;
	$totalBizShare = 0;
	$totalCharityAmount = 0;
	$totalManagerAmount = 0;
	$totalEmployeeAmount = 0;

	foreach ($orders as $order) {
	    $getCreatedAt = $order->getCreatedAt();
	    $date = $getCreatedAt->format('Y-m-d');
           // dd($order->getUser()->getBusinesses());
            foreach($order->getUser()->getBusinesses() as $buyer_business){
                $buyer_company_id= $buyer_business->getId();
            }
            foreach($this->getUser()->getBusinesses() as $current_user_business){
                $current_user_company = $current_user_business->getId();
            } 
            
            if($buyer_company_id!=$current_user_company && $current_user_company!=''&& $buyer_company_id!=''){
                continue;
            }

	    foreach ($order->getShopItems() as $key => $shopItems) {
		$name = '';
                $buyer_company_id= $current_user_company='';
		if ($key == 0) {
		    $name = $order->getUser()->getFirstName() . ' ' . $order->getUser()->getLastName();
                    
		}
                
		$shop_title = $shopItems->getTitle();
		$price = number_format($shopItems->getPrice() * $shopItems->getQuantity(),2);
		$getRebatePercent = $shopItems->getRebatePercent();
		$rebateAmount = number_format((($price * $shopItems->getRebatePercent()) / 100),2);
		$bizShare = ($rebateAmount * 25) / 100;
		$bizShare = number_format($rebateAmount - $bizShare,2);
		if (!empty($shopItems->getOrder()->getSplitHystory())) {
		    $charityAmount = number_format($bizShare * $shopItems->getOrder()->getSplitHystory()->getCharityPercent() / 100,2);
		    $managerAmount = number_format($bizShare * $shopItems->getOrder()->getSplitHystory()->getBusinessPercent() / 100,2);
		    $employeeAmount = number_format($bizShare * $shopItems->getOrder()->getSplitHystory()->getEmployeePercent() / 100,2);
		}
		// Totals to show in the footer
		$totalPrice  = $totalPrice+$price;
		$totalRebateAmount+= $rebateAmount;
		$totalBizShare+=$bizShare;
		$totalCharityAmount+=$charityAmount;
		$totalManagerAmount+=$managerAmount;
		$totalEmployeeAmount+= $employeeAmount;

		$data[] = [
		    '',
		    $name,
		    $date,
		    $shop_title,
		    $price,
		    $getRebatePercent,
		    $rebateAmount,
		    $bizShare,
		    $managerAmount,
		    $charityAmount,
		    $employeeAmount
		];
	    }

        if(!empty($params['order'][0])) {
            $sorter = $params['order'][0];
            $columnNumber = $sorter['column'];
            $sortDir = $sorter['dir'];
            usort($data, array(new SortHelper($columnNumber, $sortDir), "call"));
        }

	    $result ['data'] = $data;
	    $result ['totalPrice'] = '$'.number_format($totalPrice,2);
	    $result['totalRebateAmount'] = '$'.number_format($totalRebateAmount,2);
	    $result['totalBizShare'] = '$'.number_format($totalBizShare,2);
	    $result['totalCharityAmount'] = '$'.number_format($totalCharityAmount,2);
	    $result['totalManagerAmount'] = '$'.number_format($totalManagerAmount,2);
	    $result['totalEmployeeAmount'] = '$'.number_format($totalEmployeeAmount,2);
            
	}


	// Return JSON
	return new Response(json_encode($result));
    }


    public function adminListAction(Request $request){
	// Get request params
	$params = $request->query->all();

	if (isset($params['from'])) {
	    if ($params['from'] == '') {
		unset($params['from']);
	    } else {
		$params['from'] = date_create_from_format('m/d/Y H:i:s', $params['from'] . ' 00:00:00');
		$searchField = true;
	    }
	}

	if (isset($params['to'])) {
	    if ($params['to'] == '') {
		unset($params['to']);
	    } else {
		$params['to'] = date_create_from_format('m/d/Y H:i:s', $params['to'] . ' 23:59:59');
		$searchField = true;
	    }
	}

//	$orders = $orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
//		->findBy(
//		['status' => 'paid',], ['createdAt' => 'desc']
//	);
	$business = $this->getBusiness();
	$orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
		->findForBusiness($business, $params);
	// Put default values for DataTable response
	$result = [
	    'draw' => (int) $params['draw'],
	    'data' => '',
	    'recordsTotal' => count($orders),
	    'recordsFiltered' => count($orders),
	];

	$charityAmount = 0;
	$managerAmount = 0;
	$employeeAmount = 0;
	$totalPrice = 0;
	$totalRebateAmount = 0;
	$totalBizShare = 0;
	$totalCharityAmount = 0;
	$totalManagerAmount = 0;
	$totalEmployeeAmount = 0;
	$totalResidualAmount = 0;
	$totalLbAmount = 0;
	$defaultResidual=10;
	//$totalStripeAmount = 0;

	foreach ($orders as $order) {
	    $getCreatedAt = $order->getCreatedAt();
	    $date = $getCreatedAt->format('Y-m-d');


	    foreach ($order->getShopItems() as $key => $shopItems) {
		$name = '';
		if ($key == 0) {
		    $name = $order->getUser()->getFirstName() . ' ' . $order->getUser()->getLastName();
		}
		$business_name= $order->getBusiness()->getName();
		$shop_title = $shopItems->getTitle();
		$price = number_format($shopItems->getPrice() * $shopItems->getQuantity(),2);
		$getRebatePercent = $shopItems->getRebatePercent();
		$rebateAmount = number_format((($price * $shopItems->getRebatePercent()) / 100),2);
		$lbShare = ($rebateAmount * 25) / 100;
		$residualStatus= $order->getBusiness()->getResidualStatus();
		if($residualStatus){
		    if($order->getBusiness()->getResidualAmount())
			$residualShare= $order->getBusiness()->getResidualAmount();
		    else
			$residualShare= $defaultResidual;
		}
		else{
		    echo 'Not Applicable';
		}
		$residual = number_format(($residualShare * $lbShare)/100,2) ;
		$bizShare = number_format($rebateAmount - $lbShare,2);
		//$stripeFee = number_format(($price * (2.9 / 100) + 0.3),2);
		$lb_left = number_format($lbShare - $residual,2);

		if (!empty($shopItems->getOrder()->getSplitHystory())) {
		    $charityAmount = number_format($bizShare * $shopItems->getOrder()->getSplitHystory()->getCharityPercent() / 100,2);
		    $managerAmount = number_format($bizShare * $shopItems->getOrder()->getSplitHystory()->getBusinessPercent() / 100,2);
		    $employeeAmount = number_format($bizShare * $shopItems->getOrder()->getSplitHystory()->getEmployeePercent() / 100,2);
		}
		// Totals to show in the footer
		$totalPrice  = $totalPrice+$price;
		$totalRebateAmount+= $rebateAmount;
		$totalBizShare+=$bizShare;
		$totalCharityAmount+=$charityAmount;
		$totalManagerAmount+=$managerAmount;
		$totalEmployeeAmount+= $employeeAmount;
		$totalResidualAmount+= $residual;
		$totalLbAmount+= $lb_left;

		$data[] = [
		    '',
		    $name,
		    $business_name,
		    $date,
		    $shop_title,
		    '$' . $price,
		    $getRebatePercent . '%',
		    '$' . $rebateAmount,
		    '$' . $bizShare,
		    '$' . $managerAmount,
		    '$' . $charityAmount,
		    '$' . $employeeAmount,
		    '$' . $residual,
		    '$' . $lb_left
		];
	    }
	    $result ['data'] = $data;
	    $result ['totalPrice'] = '$'.number_format($totalPrice,2);
	    $result['totalRebateAmount'] = '$'.number_format($totalRebateAmount,2);
	    $result['totalBizShare'] = '$'.number_format($totalBizShare,2);
	    $result['totalCharityAmount'] = '$'.number_format($totalCharityAmount,2);
	    $result['totalManagerAmount'] = '$'.number_format($totalManagerAmount,2);
	    $result['totalEmployeeAmount'] = '$'.number_format($totalEmployeeAmount,2);
	    $result['totalResidualAmount'] = '$'.number_format($totalResidualAmount,2);
	    $result['totalLbAmount'] = '$'.number_format($totalLbAmount,2);
	    //$result['totalStripeAmount'] = '$'.number_format($totalStripeAmount,2);
	}
	return new Response(json_encode($result));
    }
 }
