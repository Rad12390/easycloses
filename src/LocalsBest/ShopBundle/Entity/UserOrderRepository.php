<?php

namespace LocalsBest\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\Plugin;

/**
 * UserOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserOrderRepository extends EntityRepository {

    public function getLastOrder($user) {
	$qb = $this->createQueryBuilder('o');

	$qb
		->where('o.status = :status')
		->andWhere('o.user = :user')
		->orderBy('o.createdAt', 'DESC')
		->setMaxResults(1)
		->setParameter('status', 'unpaid')
		->setParameter('user', $user)
	;

	return $qb->getQuery()->getSingleResult();
    }

    public function findFoVendor($user) {
	$qb = $this->createQueryBuilder('o');

	$qb
		->join('o.shopItems', 'i')
		->join('i.sku', 's')
		->leftJoin('s.combo', 'c')
		->leftJoin('s.package', 'p')
		->where('o.status = :status')
		->andWhere(
			$qb->expr()->orX(
				'p.createdBy = :user', 'c.createdBy = :user'
			)
		)
		->orderBy('o.createdAt', 'DESC')
		->setParameter('status', 'paid')
		->setParameter('user', $user)
	;

	return $qb->getQuery()->getResult();
    }

    public function findForBusiness($business  , $params) {
	//dd($business);
	$qb = $this->createQueryBuilder('o');

	$qb
		->join('o.shopItems', 'i')
		->join('i.sku', 's')
                ->join('s.package', 'p')
                ->join('p.createdBy', 'u')
                ->join('o.user', 'u1')
                ->join('u1.createdBy', 'u2')
		->where('o.status = :status')
		->andWhere('u.owner = :businessId OR u2.owner = :businessId')
		->orderBy('o.createdAt', 'DESC')
		->setParameter('status', 'paid')
		->setParameter('businessId', $business->getId())
	;
	   if (isset($params['from'])) {
            $qb
                ->andWhere('o.createdAt >= :from')
                ->setParameter('from', $params['from']);
        }

        if (isset($params['to'])) {
            $qb
                ->andWhere('o.createdAt <= :to')
                ->setParameter('to', $params['to']);
        }
        
      //  dd($qb->getQuery()->getDQL());
	return $qb->getQuery()->getResult();
    }
    
    public function findPayoutSummary($business, $parms) {
	
	$qb = $this->createQueryBuilder('o');

	$qb
		->join('o.user', 'u')
		->join('u.businesses', 'b')
		->where('o.status = :status')
		//->andWhere('o.createdAt BETWEEN :from AND :to')
		->orderBy('o.createdAt', 'DESC')
		->setParameter('status', 'paid')
//		->setParameter('to', $parms['to'])
//		->setParameter('from', $parms['from'])
	;
	
	if (!empty($parms['from'])) {
            $qb
                ->andWhere('o.createdAt >= :from')
                ->setParameter('from', $parms['from']);
        }

        if (!empty($parms['to'])) {
            $qb
                ->andWhere('o.createdAt <= :to')
                ->setParameter('to', $parms['to']);
        }



	$orders = $qb->getQuery()->getResult();
	$manager= $this->findManagerSummary($business,$orders,$parms);
	$employee= $this->findEmployeeSummary($business,$orders,$parms);
	$charity= $this->findCharitySummary($business,$orders,$parms);
	$residual= $this->findResidualSummary($business,$orders,$parms);
	$result= array_merge($manager,$employee,$charity,$residual);
	return $result;
	
    }
    
    /* Function to find payouts for manager */
    
    public function findManagerSummary($business,$orders, $parms) {
	$lbPercent = 25;
	$result = array();
	$users = array();
	foreach ($orders as $order) {
	    $ownerId = $order->getBusiness()->getOwner()->getId();
	    $businessPercent = 0;
	    $total = 0;
	    $targettwo = 'user';
	    $address = $order->getBusiness()->getAddress()->getStreet() . ' , ' . $order->getBusiness()->getAddress()->getCity() . ' , ' . $order->getBusiness()->getAddress()->getState() . ' , ' . $order->getBusiness()->getAddress()->getZip();



	    if ($order->getSplitHystory()) {
		$businessPercent = $order->getSplitHystory()->getBusinessPercent();
		$targettwo = $order->getSplitHystory()->getTargettwo();
	    }

	    if ($targettwo == 'user') {
		$payee = $order->getBusiness()->getOwner()->getFirstname() . ' ' . $order->getBusiness()->getOwner()->getLastname();
	    } else {
		$payee = $order->getBusiness()->getName();
	    }


	    foreach ($order->getShopItems() as $items) {
		$price = $items->getPrice() * $items->getQuantity();
		$rebateAmount = ($price * $items->getRebatePercent()) / 100;
		$lbShare = ($rebateAmount * $lbPercent) / 100;
		$bizShare = $rebateAmount - $lbShare;
		$total = $total + ($bizShare * $businessPercent / 100);
	    }

	    $summary[] = [ 'id' => $ownerId,
		'payee' => $payee,
		'businessName' => $order->getBusiness()->getName(),
		'total' => $total,
		'address' => $address,
		'targettwo' => $targettwo,
                'date' => $order->getCreatedAt()->format('Y-m-d H:i:s')
            ];
	    
	    //users details array
	    $users[$ownerId] = [
		'name' => $payee
	    ];
	}
//	echo '<pre>';
//	    print_r($summary);
//	    die;
	if ($users) {
	    foreach ($users as $users_key => $users_data) {
		$count = 0;
		$count_new = 0;
		$counter=0;
		$user_count=0;
		$history = array();
		foreach ($summary as $summary_key => $summary_data) {
		    if ($users_key == $summary_data['id']) {
			if ($summary_data['targettwo'] == 'employee') {
			    $counter+=$summary_data['total'];
			    if($summary_data['total']){
				$history1[] = [
				    'id' => $summary_data['id'],
				    'payee' => $summary_data['payee'],
				    'businessName' => $summary_data['businessName'],
				    'total' => $summary_data['total'],
				    'address' => $summary_data['address'],
                                    'date' => $summary_data['date']
				];
			    }
			    if($counter){
				$newdata =  array (
				'id' => $summary_data['id'],
				'payee' => $summary_data['payee'],
				'businessName' => $summary_data['businessName'],
				'total' => $counter,
				'address' => $summary_data['address'],
				'history' => $history1,
                                'date' => $summary_data['date'],
				'method' => 'Manager'
			      );
				if($count==0)
				    array_push($result,$newdata);
				else
				    $result[$user_count]= $newdata;
			    }
			    $count++;
			    continue;
			}
			$id = $summary_data['id'];
			$payee = $summary_data['payee'];
			$address = $summary_data['address'];
			$business = $summary_data['businessName'];
                        $date= $summary_data['date'];
			$count_new+=$summary_data['total'];
			if($summary_data['total']){
			    $history[] = [
				'id' => $id,
				'payee' => $payee,
				'businessName' => $business,
				'total' => $summary_data['total'],
				'address' => $address,
                                'date'=> $date
			    ];
			}
		    }
		}
		$user_count++;
		if($count_new){
		    $result[] = [
			'id' => $id,
			'payee' => $payee,
			'businessName' => $business,
			'total' => $count_new,
			'address' => $address,
			'history' => $history,
			'method'=> 'Manager',
                        'date'=> $date
		    ];
		}
	    }
	}
       
	return $result;
	
    }
    
    /* Function to find payouts for Employee */
    
    public function findEmployeeSummary($business,$orders, $parms){
	$lbPercent = 25;
	
	$result = array();
	$users = array();
       // dd($orders);
	foreach ($orders as $order) {
           
            
	    $ownerId = $order->getBusiness()->getOwner()->getId();
	    $charityPercent = 0;
	    $total = 0;
	    $targettwo = 'user';
	    $address = $order->getBusiness()->getAddress()->getStreet() . ' , ' . $order->getBusiness()->getAddress()->getCity() . ' , ' . $order->getBusiness()->getAddress()->getState() . ' , ' . $order->getBusiness()->getAddress()->getZip();



	    if ($order->getSplitHystory()) {
		$charityPercent = $order->getSplitHystory()->getEmployeePercent();
		$targettwo = $order->getSplitHystory()->getTargettwo();
	    }

	    if ($targettwo == 'user') {
		$payee = $order->getUser()->getFirstname() . ' ' . $order->getUser()->getLastname();
	    } else {
		$payee = $order->getBusiness()->getName();
	    }


	    foreach ($order->getShopItems() as $items) {
		$price = $items->getPrice() * $items->getQuantity();
		$rebateAmount = ($price * $items->getRebatePercent()) / 100;
		$lbShare = ($rebateAmount * $lbPercent) / 100;
		$bizShare = $rebateAmount - $lbShare;
		$total = $total + ($bizShare * $charityPercent / 100);
	    }
            
            $business_name='';
            
            if($order->getUser()->getOwner()!=NULL)
                $business_name= $order->getUser()->getOwner()->getName();
            
	    $summary[] = [ 'id' => $ownerId,
		'payee' => $payee,
		'businessName' => $business_name,
		//'businessName' => '',
		'total' => $total,
		'address' => $address,
		'targettwo' => $targettwo,
                'date' => $order->getCreatedAt()->format('Y-m-d H:i:s')
            ];
	    
	    //users details array
	    $users[$ownerId] = [
		'name' => $payee
	    ];
	}
        
//	echo '<pre>';
//	    print_r($summary);
//	    die;
	if ($users) {
	    foreach ($users as $users_key => $users_data) {
		$count = 0;
		$count_new = 0;
		$counter=0;
		$user_count=0;
		$history = array();
		foreach ($summary as $summary_key => $summary_data) {
		    if ($users_key == $summary_data['id']) {
			if ($summary_data['targettwo'] == 'employee') {
			    $counter+=$summary_data['total'];
			    if($summary_data['total']){
				$history1[] = [
				    'id' => $summary_data['id'],
				    'payee' => $summary_data['payee'],
				    'businessName' => $summary_data['businessName'],
				    'total' => $summary_data['total'],
				    'address' => $summary_data['address'],
                                    'date' => $summary_data['date']
				];
			    }
			    if($counter){
				$newdata =  array (
				'id' => $summary_data['id'],
				'payee' => $summary_data['payee'],
				'businessName' => $summary_data['businessName'],
				'total' => $counter,
				'address' => $summary_data['address'],
				'history' => $history1,
				'method' => 'Employee',
                                'date' => $summary_data['date']
			      );
				if($count==0)
				    array_push($result,$newdata);
				else
				    $result[$user_count]= $newdata;
			    }
			    $count++;
			    continue;
			}
			$id = $summary_data['id'];
			$payee = $summary_data['payee'];
			$address = $summary_data['address'];
			$business = $summary_data['businessName'];
                        $date = $summary_data['date'];
			$count_new+=$summary_data['total'];
			if($summary_data['total']){
			    $history[] = [
				'id' => $id,
				'payee' => $payee,
				'businessName' => $business,
				'total' => $summary_data['total'],
				'address' => $address,
                                'date' => $date
			    ];
			}
		    }
		}
		$user_count++;
		if($count_new){
		    $result[] = [
			'id' => $id,
			'payee' => $payee,
			'businessName' => $business,
			'total' => $count_new,
			'address' => $address,
			'history' => $history,
			'method' => 'Employee',
                        'date' => $date
		    ];
		}
	    }
	}

	return $result;
	
    }
    
    /* Function to find payouts for charity */
    
    public function findCharitySummary($business,$orders, $parms){
	$lbPercent = 25;
	
	$result = array();
	$users = array();
	foreach ($orders as $order) {
	  
	    $charityPercent = 0;
	    
	    $targettwo = 'user';
	    $address = $order->getBusiness()->getAddress()->getStreet() . ' , ' . $order->getBusiness()->getAddress()->getCity() . ' , ' . $order->getBusiness()->getAddress()->getState() . ' , ' . $order->getBusiness()->getAddress()->getZip();



	    if ($order->getSplitHystory()) {
		$charityPercent = $order->getSplitHystory()->getCharityPercent();
		
		$targettwo = $order->getSplitHystory()->getTarget();
	    }
	    
	  
	    if ($targettwo == 'user') {
		
		//$payee = $order->getBusiness()->getOwner()->getFirstname() . ' ' . $order->getBusiness()->getOwner()->getLastname();
		//loop to iterate charities
		$count_charity= count($order->getCharitiesHistory());
		
		foreach($order->getCharitiesHistory() as $charity){
		    $total = 0;
		    $payee = $charity->getCharityName();
		    $ownerId = $charity->getCharityId()->getId();
		   
		    foreach ($order->getShopItems() as $items) {
			$price = $items->getPrice() * $items->getQuantity();
			$rebateAmount = ($price * $items->getRebatePercent()) / 100;
			$lbShare = ($rebateAmount * $lbPercent) / 100;
			$bizShare = $rebateAmount - $lbShare;
			$total = ($total + ($bizShare * $charityPercent / 100));
		    }
		    
		    $summary[] = [ 'id' => $ownerId,
			'payee' => $payee,
			'businessName' => $order->getBusiness()->getName(),
			'total' => $total/$count_charity,
			'address' => $address,
			'targettwo' => $targettwo,
                        'date' => $order->getCreatedAt()->format('Y-m-d H:i:s')
                    ];
		    //users details array
		    $users[$ownerId] = [
			'name' => $payee
		    ];
		}
	    } else {
		
		$ownerId = $order->getBusiness()->getOwner()->getId();
		$payee = $order->getBusiness()->getName();
		foreach ($order->getShopItems() as $items) {
		    $price = $items->getPrice() * $items->getQuantity();
		    $rebateAmount = ($price * $items->getRebatePercent()) / 100;
		    $lbShare = ($rebateAmount * $lbPercent) / 100;
		    $bizShare = $rebateAmount - $lbShare;
		    $total = $total + ($bizShare * $charityPercent / 100);
		}

		$summary[] = [ 'id' => $ownerId,
		    'payee' => $payee,
		    'businessName' => $order->getBusiness()->getName(),
		    'total' => $total,
		    'address' => $address,
		    'targettwo' => $targettwo,
                    'date' => $order->getCreatedAt()->format('Y-m-d H:i:s')
                ];
		
		//users details array
		$users[$ownerId] = [
		    'name' => $payee
		];
	    }
	    
		  
	}
	
	if ($users) {
	    foreach ($users as $users_key => $users_data) {
		$count = 0;
		$count_new = 0;
		$counter=0;
		$user_count=0;
		$history = array();
		foreach ($summary as $summary_key => $summary_data) {
		    if ($users_key == $summary_data['id']) {
			if ($summary_data['targettwo'] == 'employee') {
			    $counter+=$summary_data['total'];
			    if($summary_data['total']){
				$history1[] = [
				    'id' => $summary_data['id'],
				    'payee' => $summary_data['payee'],
				    'businessName' => $summary_data['businessName'],
				    'total' => $summary_data['total'],
				    'address' => $summary_data['address'],
                                    'date' => $summary_data['date']
				];
			    }
			    if($counter){
				$newdata =  array (
				'id' => $summary_data['id'],
				'payee' => $summary_data['payee'],
				'businessName' => $summary_data['businessName'],
				'total' => $counter,
				'address' => $summary_data['address'],
				'history' => $history1,
				'method' => 'Charity',
                                'date' => $summary_data['date']
			      );
				if($count==0)
				    array_push($result,$newdata);
				else
				    $result[$user_count]= $newdata;
			    }
			    $count++;
			    continue;
			}
			$id = $summary_data['id'];
			$payee = $summary_data['payee'];
			$address = $summary_data['address'];
			$business = $summary_data['businessName'];
                        $date = $summary_data['date'];
			$count_new+=$summary_data['total'];
			if($summary_data['total']){
			    $history[] = [
				'id' => $id,
				'payee' => $payee,
				'businessName' => $business,
				'total' => $summary_data['total'],
				'address' => $address,
                                'date' => $date
			    ];
			}
		    }
		}
		$user_count++;
		if($count_new){
		$result[] = [
		    'id' => $id,
		    'payee' => $payee,
		    'businessName' => $business,
		    'total' => $count_new,
		    'address' => $address,
		    'history' => $history,
		    'method' => 'Charity',
                    'date' => $date
		];
	    }
	    }
	}

	return $result;
    }
    
    /* Function to find payouts for residual */
    
    public function findResidualSummary($business,$orders, $parms){
	$lbPercent = 25;
	$defaultResidual= 10;
	
	$result = array();
	$users = array();
	foreach ($orders as $order) {
	    $ownerId = $order->getBusiness()->getOwner()->getId();
	    $charityPercent = 0;
	    $total = 0;
	    $targettwo = 'user';
	    $address = $order->getBusiness()->getAddress()->getStreet() . ' , ' . $order->getBusiness()->getAddress()->getCity() . ' , ' . $order->getBusiness()->getAddress()->getState() . ' , ' . $order->getBusiness()->getAddress()->getZip();



	    if ($order->getSplitHystory()) {
		$charityPercent = $order->getSplitHystory()->getCharityPercent();
		$targettwo = $order->getSplitHystory()->getTargettwo();
	    }

//	    if ($targettwo == 'user') {
//		$payee = $order->getBusiness()->getOwner()->getFirstname() . ' ' . $order->getBusiness()->getOwner()->getLastname();
//	    } else {
//		$payee = $order->getBusiness()->getName();
//	    }
	    $payee = $order->getUser()->getCreatedBy()->getFirstname() . ' ' . $order->getUser()->getCreatedBy()->getLastname();
	    
	    foreach ($order->getShopItems() as $items) {
		$price = $items->getPrice() * $items->getQuantity();
		$rebateAmount = ($price * $items->getRebatePercent()) / 100;
		$lbShare = ($rebateAmount * $lbPercent) / 100;
		$residualStatus= $order->getBusiness()->getResidualStatus();
		
		if($residualStatus){
		    if($order->getBusiness()->getResidualAmount())
			$residualShare= $order->getBusiness()->getResidualAmount();
		    else
			$residualShare= $defaultResidual;
		}
		else{
		    return [];
		}
		$total = ($residualShare * $lbShare)/100 ;  //residual amount calculation
	    }

	    $summary[] = [ 'id' => $ownerId,
		'payee' => $payee,
		'businessName' => $order->getUser()->getCreatedBy()->getOwner()? $order->getUser()->getCreatedBy()->getOwner()->getName() :'',
		'total' => $total,
		'address' => $address,
		'targettwo' => $targettwo,
                'date' => $order->getCreatedAt()->format('Y-m-d H:i:s')
            ];
	    
	    //users details array
	    $users[$ownerId] = [
		'name' => $payee
	    ];
	}
	
	if ($users) {
	    foreach ($users as $users_key => $users_data) {
		$count = 0;
		$count_new = 0;
		$counter=0;
		$user_count=0;
		$history = array();
		foreach ($summary as $summary_key => $summary_data) {
		    if ($users_key == $summary_data['id']) {
			if ($summary_data['targettwo'] == 'employee') {
			    $counter+=$summary_data['total'];
			    if($summary_data['total']){
				$history1[] = [
				    'id' => $summary_data['id'],
				    'payee' => $summary_data['payee'],
				    'businessName' => $summary_data['businessName'],
				    'total' => $summary_data['total'],
				    'address' => $summary_data['address'],
                                    'date' => $summary_data['date']
				];
			    }
			    if($counter){
				$newdata =  array (
				'id' => $summary_data['id'],
				'payee' => $summary_data['payee'],
				'businessName' => $summary_data['businessName'],
				'total' => $counter,
				'address' => $summary_data['address'],
				'history' => $history1,
				'method' => 'Residual',
                                'date' => $summary_data['date']
			      );
				if($count==0)
				    array_push($result,$newdata);
				else
				    $result[$user_count]= $newdata;
			    }
			    $count++;
			    continue;
			}
			$id = $summary_data['id'];
			$payee = $summary_data['payee'];
			$address = $summary_data['address'];
			$business = $summary_data['businessName'];
                        $date = $summary_data['date'];
			$count_new+=$summary_data['total'];
			//create array for history 
			if($summary_data['total']){
			    $history[] = [
				'id' => $id,
				'payee' => $payee,
				'businessName' => $business,
				'total' => $summary_data['total'],
				'address' => $address,
                                'date' => $date
			    ];
			}
		    }
		}
		$user_count++;
		if($count_new){
		    $result[] = [
			'id' => $id,
			'payee' => $payee,
			'businessName' => $business,
			'total' => $count_new,
			'address' => $address,
			'history' => $history,
			'method' => 'Residual',
                        'date' => $date
		    ];
		}
	    }
	}

	return $result;
    }

}
