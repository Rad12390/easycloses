<?php

namespace LocalsBest\ShopBundle\Controller;

use DateTime;
use Exception;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Disposition;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\ItemSet;
use LocalsBest\ShopBundle\Entity\OrderItem;
use LocalsBest\ShopBundle\Entity\Payment;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\UserOrder;
use LocalsBest\ShopBundle\Entity\UserPlugin;
use LocalsBest\SocialMediaBundle\Entity\BucketSchedulerSet;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\Job;
use LocalsBest\UserBundle\Entity\JobContact;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('@LocalsBestShop/payment/index.html.twig', [
            'params' => http_build_query($request->query->all()),
        ]);
    }

    /**
     * Charge process. Create Payment for Order
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function chargeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $orderCode = $request->request->get('orderCode');
        $couponCode = $request->request->get('couponCode', null);
        $order = $em->getRepository('LocalsBestShopBundle:UserOrder')->findOneBy(['code' => $orderCode]);

        // TODO: need functionality that will check coupon for skus that user want to buy
        // decrease payment amount
        // how we going to save order amount???

        if ($order->getTotal() > 0) {

            if ($request->isMethod('POST')) {
                $StripToken = $request->request->get('stripeToken');
                if ($StripToken) {
                    try {
                        $totalAmount = $order->getTotal();
                        $shopItems = $order->getShopItems();

                        $products = [];

                        /** @var OrderItem $shopItem */
                        foreach ($shopItems as $shopItem) {

                            $array = [];
                            $array['product_id'] = $shopItem->getSku()->getId();
                            $array['product'] = $shopItem->getSku()->getTitle();
                            $array['price'] = $shopItem->getPriceObject()->getAmount();
                            $array['sku'] = $shopItem->getSku();
                            $array['quantity'] = $shopItem->getQuantity();
                            $array['vendor'] = $shopItem->getSku()->getCreatedBy();
                            $array['type'] = $shopItem->getPriceObject()->getType();
                            $array['stripeplanid'] = $shopItem->getPriceObject()->getStripeplanid();
                            $array['account_id'] = $shopItem->getSku()->getCreatedBy()->getStripeAccountId();
                            $array['rebate'] = $shopItem->getPriceObject()->getRebate();
                            $array['retailprice'] = $shopItem->getPriceObject()->getretailPrice();

                            $productEntity = $shopItem->getSku()->getProductEntity();

                            if ($productEntity->getQuantity() < $shopItem->getQuantity()) {
                                $this->addFlash(
                                    'danger', 'Unfortunately there are only ' . $productEntity->getQuantity() . ' "' . $shopItem->getSku()->getTitle() . '" left in stock.'
                                );
                                return $this->redirectToRoute('checkout');
                            }
                            $productEntity->setQuantity($productEntity->getQuantity() - $shopItem->getQuantity());
                            $products[] = $array;
                        }

                        $result = $this->get('app.client.stripe')
                            ->createPremiumCharge($this->getUser(), $StripToken, $products);

                        $payment = new Payment();

                        $payment->setOrder($order);
                        $payment->setAmount($totalAmount);
                        $em->persist($payment);

                        /* Need to Save these 5 details in Order table with the Order */
                        $order->setStripeTokenId($StripToken);
                        $order->setStatus('paid');
                        $order_id = $order->getId();
                        if (
                            isset($result->simplepayment) && (is_array($result->simplepayment) and ! empty($result->simplepayment))
                        ) {
                            $simplepayment = $result->simplepayment;
                            $repository = $this->getDoctrine()->getRepository('LocalsBestShopBundle:OrderItem');
                            foreach ($simplepayment as $orderitem) {
                                $OrderItem = $repository->findOneBy(array("order" => $order, "sku" => $orderitem->product_id));
                                $OrderItem->setOrderid($order_id);
                                $OrderItem->setTxnid($orderitem->id);
                                $OrderItem->setObjecttype($orderitem->object);
                                $OrderItem->setApplicationfee($orderitem->application_fee);
                                $OrderItem->setStatus($orderitem->status);
                                $OrderItem->setProductid($orderitem->product_id);

                                $em->flush();
                            }
                        }

                        if (
                            isset($result->subscription) && (is_array($result->subscription) and ! empty($result->subscription))
                        ) {
                            $subscription = $result->subscription;
                            $repository = $this->getDoctrine()->getRepository('LocalsBestShopBundle:OrderItem');
                            foreach ($subscription as $orderitem) {
                                $OrderItem = $repository->findOneBy(array("order" => $order, "sku" => $orderitem->product_id));
                                $OrderItem->setOrderid($order_id);
                                $OrderItem->setTxnid($orderitem->id);
                                $OrderItem->setObjecttype($orderitem->object);
                                $OrderItem->setApplicationfee($orderitem->application_fee_percent);
                                $OrderItem->setStatus($orderitem->status);
                                $OrderItem->setProductid($orderitem->product_id);

                                $newPeriodEnd = DateTime::createFromFormat('U', $orderitem->current_period_end);
                                $OrderItem->setSubscriptionEndedAt($newPeriodEnd);
                                $OrderItem->setStatus($orderitem->status);
                                $OrderItem->setSubscriptionstatus(1);

                                $em->flush();
                            }
                        }
                        $em->flush();
                    } catch (Exception $e) {
                        $this->addFlash(
                            'warning', sprintf('Unable to take payment, %s', lcfirst($e->getMessage() . ', please try again.'))
                        );

                        return $this->redirectToRoute('checkout', ['code' => $order->getCode()]);
                    }
                    $success_status = $this->orderDispositionsHandler($order, $payment);
                }
            }
        } else {
            $success_status = $this->orderDispositionsHandler($order, null);
        }
        $this->addFlash('success_status', $success_status);
        return $this->redirectToRoute('shop_payment_success');
    }

    /**
     * Show success payment page
     *
     * @return Response
     */
    public function successAction()
    {
        foreach ($this->get('session')->getFlashBag()->get('success_status') as $status) {
            $status = $status;
        }

        //get if have accepted terms & conditions
        $user = $this->getUser();
        $terms_status = 0;
        if (!empty($user->getTerms())) {
            $terms_status = 1;
        }

        return $this->render('@LocalsBestShopBundle/payment/success.html.twig', [
            'user' => $this->getUser(),
            'status' => $status,
            'term_status' => $terms_status
        ]);
    }

    /**
     * Manual activation for special type of objects
     *
     * @param Request $request
     * @param $skuId
     *
     * @return RedirectResponse
     */
    public function customActivationAction(Request $request, $skuId)
    {
        $em = $this->getDoctrine()->getManager();
        $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($skuId);
        $neededDisposition = $request->query->get('disposition', null);

        $this->infoDispositionHandler($sku, $this->getUser(), $neededDisposition);
        //add contact to vendor
        $params['user'] = $this->getUser();
        $params['vendor'] = $sku->getPackage()->getCreatedBy();
        $package_name = $sku->getPackage()->getTitle();
        $this->createContact($params);
        //add a closed info type job
        $this->createJob($params);
        //add ec bell notification
        $message = 'Your Info link Package #' . $package_name . ' was bought.';
        // Send Notification about this
        $this->get('localsbest.notification')
            ->addNotification(
                $message, 'sku_show', array('id' => $sku->getId()), array($params['vendor']), array($params['user'])
            )
        ;
        $this->addFlash(
            'success', sprintf('Thank you! You have been sent an email with an info link')
        );
        $referrer = $request->headers->get('referer');
        return new RedirectResponse($referrer);
    }

    /**
     * Activate dispositions for selected Order entity
     *
     * @param UserOrder $order
     * @param Payment|null $payment
     */
    private function orderDispositionsHandler(UserOrder $order, Payment $payment = null)
    {
        foreach ($order->getShopItems() as $package) {
            $package_id = $package->getSku()->getPackage()->getId();
        }

        $shopItems = $order->getShopItems();
        $user = $order->getUser();

        /** @var OrderItem $shopItem */
        $success_status = 0;
        foreach ($shopItems as $shopItem) {
            $sku = $shopItem->getSku();
            $success_status = $this->skuDispositionsHandler($sku, $user, $payment, null, $package_id);
        }
        return $success_status;
    }

    /**
     * Activate dispositions for selected Sku entity
     *
     * @param Sku $sku
     * @param User $user
     * @param Payment|null $payment
     * @param null $neededDisposition
     */
    private function skuDispositionsHandler(Sku $sku, User $user, Payment $payment = null, $neededDisposition = null, $package_id)
    {

        $em = $this->getDoctrine()->getManager();
        $itemsSets = $sku->getItemsSets();
        $success_status = 0;
        /** @var ItemSet $itemSet */
        $count = 0;
        foreach ($itemsSets as $itemSet) {
            $item = $itemSet->getItem();
            $itemType = $item->getType();
            $vendor = $item->getCreatedBy();
            $useslimit = $itemSet->getUsesLimit();

            $params = [
                'vendor' => $vendor,
                'user' => $user,
                'item' => $item,
                'sku' => $sku,
                'package_id' => $package_id,
                'shop_items' => $itemsSets,
            ];

            if ($count < 1) {
                $this->createNotification($params);
                $this->sendEmail($params, 'client');
                $this->sendEmail($params, 'vendor');
                $this->sendText($params, 'client');
                $this->sendText($params, 'vendor');
                $count++;
            }

            switch ($itemType) {
                case Item::PLUGIN:
                    $userPlugin = new UserPlugin();
                    $userPlugin->setUser($user);
                    $userPlugin->setPlugin($item->getPlugin());
                    $userPlugin->setUsesLimit($useslimit);

                    if ($payment !== null) {
                        $userPlugin->setPayment($payment);
                    }
                    if ($item->getPlugin()->getSlug() == 'packages') {
                        $success_status = 1;
                    }
                    $em->persist($userPlugin);
                    $em->flush();
                    break;
                case Item::BUCKET:
                    $bucket = $item->getBucket();
                    $bsSet = new BucketSchedulerSet();
                    $bsSet->setUser($user);
                    $bsSet->setBucket($bucket);
                    $em->persist($bsSet);
                    $em->flush();
                    break;
            }

            $dispositions = $item->getDispositions();
            /** @var Disposition $disposition */
            foreach ($dispositions as $disposition) {
                $type = $disposition->getType();
                $options = $disposition->getOptions();

                if ($neededDisposition !== null) {
                    $type = $neededDisposition;
                }

                $params['options'] = $options;

                switch ($type) {
                    case 'job':
                        $this->createJob($params);
                        break;
                    case 'contact':
                        $this->createContact($params);
                        break;
                    case 'service_directory':
                        $this->addToServiceDirectory($params);
                        break;
                }

                if ($neededDisposition !== null) {
                    break;
                }
            }
        }
        return $success_status;
    }

    /**
     * Activate disposition for selected Info Sku entity
     *
     * @param Sku $sku
     * @param User $user
     * @param string $neededDisposition
     */
    private function infoDispositionHandler(Sku $sku, User $user, $neededDisposition)
    {
        $itemsSets = $sku->getItemsSets();

        /** @var ItemSet $itemSet */
        foreach ($itemsSets as $itemSet) {
            $item = $itemSet->getItem();
            $itemType = $item->getType();
            $vendor = $item->getCreatedBy();
            $business = $vendor->getBusinesses()->first();

            if ($itemType == Item::EXTERNAL_LINK) {
                if ($neededDisposition == 'email') {
                    $this->get('localsbest.mailman')->sendInfoEmailToClient($user, $item, $business);
                } else {
                    $phone = $user->getPrimaryPhone()->getNumber();
                    $message = 'Hi ' . $user->getFullName() . '. Here is info you requested from ' . $business->getName() . '. ';

                    if ($item->getExternalLink() !== null) {
                        $message .= " Link:" . $item->getExternalLink() . ' ';
                    }

                    $message .= " Thank you, LocalsBest." . $item->getExternalLink();
                    $message .= " " . $this->generateUrl('ec_shop_main_page');

                    // Send text by disposition
                    $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
                    $sender = $this->get('jhg_nexmo_sms');
                    $sender->sendText('+1' . $number, $message, null);
                }
            }
        }
    }

    /**
     * Create Job for Vendor from User
     * TODO: need to know what about job address and due date
     *
     * @param array $params
     *
     * @return bool
     */
    private function createJob($params)
    {
        $em = $this->getDoctrine()->getManager();
        $vendor = $params['vendor'];
        $user = $params['user'];
        $job = new Job();
        $result = $this->getRepository('LocalsBestUserBundle:Job')->getJobStatus(5);
        foreach ($user->getBusinesses() as $business) {
            $business = $business;
        }
        //create parent job type
        $job->setStatus($result)
            ->setJobStatus('Closed')
            ->setCreatedBy($this->getUser())
            ->setOwner($business)
            ->setVendor($params['vendor'])
            ->setOrderType("Info")
            ->setIsParent(TRUE);
        //create job contact
        $em->persist($job);
        $em->flush();

        //create actual job type
        $jobData = clone $job;
        $jobData->setIsParent(FALSE);
        $jobData->setParent($job);

        $newContact = new JobContact();
        if ($user->getOwner()) {
            $company = $user->getOwner()->getName();
        } else {
            $company = '';
        }
        $name = $user->getFullName();
        $newContact->setRole('Client')
            ->setPhone($user->getPrimaryPhone()->getNumber())
            ->setEmail($user->getPrimaryEmail()->getEmail())
            ->setCompany($company)
            ->setContactName($name)
            ->setContactBy('phone')
            ->setJob($jobData);
        $em->persist($newContact);
        $jobData->addContact($newContact);
        $em->persist($jobData);
        $em->flush();
        return true;
    }

    /**
     * Create Vendor Contact for User
     *
     * @param array $params
     *
     * @return bool
     */
    private function createContact($params)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $params['user'];
        /** @var User $vendor */
        $vendor = $params['vendor'];

        $contact = new AllContact();

        $contact
            ->setInvitation(false)
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setNumber($user->getPrimaryPhone()->getNumber())
            ->setEmail($user->getPrimaryEmail()->getEmail())
            ->setUser($user)
            ->setCreatedBy($vendor)
        ;

        $contact
            ->setStatus($em->getRepository('LocalsBestUserBundle:AllContact')->getDefaultStatus())
        ;

        $em->getRepository('LocalsBest\UserBundle\Entity\AllContact')->save($contact);

        // Attach new Contact entity to $user
        $user->addAllContact($contact);

        $em->persist($contact);
        $em->flush();

        return true;
    }

    /**
     * Add notification for vendor about user
     *
     * @param array $params
     *
     * @return bool
     */
    private function createNotification($params)
    {
        $item = $params['item'];
        $package_id = $params['package_id'];
        $package_name = $params['sku']->getPackage()->getTitle();

        $message = 'Package #' . $package_name . ' was bought.';
        // Send Notification about this
        $this->get('localsbest.notification')
            ->addNotification(
                $message, 'sku_show', array('id' => $params['sku']->getId()), array($params['vendor']), array($params['user'])
            )
        ;

        return true;
    }

    /**
     *  Add Vendor to User Service Directory
     *
     * @param array $params
     *
     * @return bool
     */
    private function addToServiceDirectory($params)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $params['user'];
        /** @var User $vendor */
        $vendor = $params['vendor'];

        $user->setMyVendors($vendor);
        $vendor->setVendorsWithMe($user);

        $em->flush();

        return true;
    }

    /**
     * Send email by disposition
     *
     * @param array $params
     * @param string $to
     *
     * @return bool
     */
    private function sendEmail($params, $to)
    {
        if ($to == 'client') {
            $this->get('localsbest.mailman')->sendDispositionEmailToClient($params);
        } else {
            $this->get('localsbest.mailman')->sendDispositionEmailToVendor($params);
        }
        return true;
    }

    /**
     * Send text by disposition
     *
     * @param array $params
     * @para, string $to
     *
     * @return bool
     */
    private function sendText($params, $to)
    {
        $sku = $params['sku'];

        $item = $params['item'];
        $vendor = $params['vendor'];
        $user = $params['user'];

        $itemset = $sku->getItemsSets();
        $counter = 0;
        foreach ($sku->getPrices() as $price) {
            $price = $price->getretailPrice();
        }
        if ($to == 'client') {
            $phone = $user->getPrimaryPhone()->getNumber();
            //$message = 'Vendor: ' . $vendor->getFullName() . '; Info about Product #' . $sku->getId() . ";";
            $message = "Hi " . $user->getFullName() . "," . "\n";
            if ($price)
                $message .= "This is to confirm  your Order " . $sku->getPackage()->getTitle() . " from " . $vendor->getFullName() . $vendor->getPrimaryPhone()->getNumber() . " for $" . $price;
            else
                $message .= "This is to confirm  your Order " . $sku->getPackage()->getTitle() . " from " . $vendor->getFullName() . $vendor->getPrimaryPhone()->getNumber() . " for free";
            $message .= "\n Questions on this purchase?  Please contact the vendor directly so they may assist you.";
            $message .= "\n Thanks! LocalsBest \n";
            $message .= "https://app.easycloses.com/ec-shop";
        } else {
            $phone = $vendor->getPrimaryPhone()->getNumber();
            $message = "Hi " . $vendor->getFullName() . "," . "\n";
            if ($price)
                $message .= "Order Confirmation: " . $sku->getPackage()->getTitle() . " for $" . $price . " by " . $user->getFullName();
            else
                $message .= "Order Confirmation: " . $sku->getPackage()->getTitle() . " for free by " . $user->getFullName();
            $message .= "\n Thanks! LocalsBest \n";
            $message .= "https://app.easycloses.com/ec-shop/orders/vendor-list";
        }

        // Send text by disposition
        $number = str_replace(['-', ' ', '(', ')', '.'], '', $phone);
        $sender = $this->get('jhg_nexmo_sms');
        sleep(1);
        $sender->sendText('+1' . $number, $message, null);

        return true;
    }

    /**
     * payments list for admin
     *
     * @return Response
     */
    public function adminListAction(Request $request)
    {
        return $this->render('@LocalsBestShop/payment/admin-list-datatable.html.twig', [
            'params' => http_build_query($request->query->all()),
        ]);
    }

    /**
     * payments list for admin
     *
     * @return Response
     */
    public function managerListAction()
    {
        $business = $this->getBusiness();
        $orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
            ->findForBusiness($business);

        return $this->render('@LocalsBestShop/payment/manager-list.html.twig', [
                'defaultPercentage' => $this->getParameter('payment')['manager_percentage'],
                'orders' => $orders,
        ]);
    }

    public function managerListAdvanceSearchAction()
    {
        return $this->render('@LocalsBestShop/payment/advanceSearch.html.twig', [
        ]);
    }

    public function adminListAdvanceSearchAction()
    {
        return $this->render('@LocalsBestShop/payment/advanceSearch.html.twig', [
        ]);
    }

    public function termsAction()
    {
        return $this->render('@LocalsBestShop/payment/terms.html.twig', [
        ]);
    }
}
