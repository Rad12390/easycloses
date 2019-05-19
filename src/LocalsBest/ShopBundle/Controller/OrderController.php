<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Combo;
use LocalsBest\ShopBundle\Entity\ManageOrderCharities;
use LocalsBest\ShopBundle\Entity\OrderItem;
use LocalsBest\ShopBundle\Entity\Package;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\SplitPaymentHystory;
use LocalsBest\ShopBundle\Entity\UserOrder;
use Stripe\Error\Base;
use Stripe\Error\Card;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * List of Orders
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('LocalsBestShopBundle:UserOrder')->findBy(
            ['user' => $this->getUser()], ['createdAt' => 'DESC']
        );

        return $this->render('@LocalsBestShop/order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     *  Show vendors Order entities
     *
     * @return Response
     */
    public function vendorListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $orderItems = $em->getRepository('LocalsBestShopBundle:OrderItem')->findOrdered($this->getUser());

        return $this->render('@LocalsBestShop/order/vendor-list.html.twig', [
            'orderItems' => $orderItems
        ]);
    }

    /**
     * Show Order for Vendor
     *
     * @param $code
     *
     * @return Response
     */
    public function vendorShowAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('LocalsBestShopBundle:UserOrder')->findOneBy([
            'code' => $code,
            'user' => $this->getUser(),
        ]);

        return $this->render('@LocalsBestShop/order/vendor-show.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * Show Order information
     *
     * @param $code
     *
     * @return Response
     */
    public function showAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('LocalsBestShopBundle:UserOrder')->findOneBy([
            'code' => $code,
            'user' => $this->getUser(),
        ]);

        return $this->render('@LocalsBestShop/order/show.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * Refund payment action
     *
     * @param $itemid
     * @param $orderid
     *
     * @return RedirectResponse
     */
    public function refundAction($itemid, $orderid)
    {
        if (isset($itemid) and ! empty($itemid)) {
            try {
                $em = $this->getDoctrine()->getManager();

                $order = $em->getRepository('LocalsBestShopBundle:UserOrder')->findOneBy(['id' => $orderid]);
                $orderitem = $em->getRepository('LocalsBestShopBundle:OrderItem')->findOneBy(['id' => $itemid]);


                $stripeorderid = $orderitem->getTxnid();

                /** @var Package $productItem */
                $productItem = $orderitem->getSku();
                $connectid = $productItem->getCreatedBy()->getStripeAccountId();

                $this->get('app.client.stripe')->refundPremiumCharge($stripeorderid, $connectid);

                $orderitem->setRefunded(1);
                $orderitem->setStatus('Refunded');

                $em->flush();
                $this->addFlash('success', sprintf('Congrats: Refund has been done'));
                $redirect = $this->generateUrl('order_list');
                return $this->redirect($redirect);
            } catch (Base $e) {
                $this->addFlash(
                    'warning', sprintf('Unable to Refund the payment now, %s', $e instanceof Card ? lcfirst($e->getMessage()) : 'please try again.')
                );
                $redirect = $this->generateUrl('order_list');
                return $this->redirect($redirect);
            }
        }

        $redirect = $this->generateUrl('order_list');
        return $this->redirect($redirect);
    }

    /**
     * cancelsub  Order action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function cancelSubAction(Request $request)
    {
        $itemId = $request->query->get('itemid');
        if (isset($itemId) and ! empty($itemId)) {
            try {
                $em = $this->getDoctrine()->getManager();

                $orderItem = $em->getRepository('LocalsBestShopBundle:OrderItem')->find($itemId);
                $stripeOrderId = $orderItem->getTxnid();

                /** @var Package $productItem */
                $productItem = $orderItem->getSku();
                $connectAccount = $productItem->getCreatedBy()->getStripeAccountId();

                $result = $this->get('app.client.stripe')->CancelSubscription($stripeOrderId, $connectAccount);

                $orderItem->setStatus($result->status);
                $orderItem->setSubscriptionstatus(1);

                $em->flush();

                $this->addFlash('success', 'Congrats: Subscription has been Cancelled');
                return $this->redirectToRoute('order_list');
            } catch (Base $e) {
                $this->addFlash(
                    'warning', sprintf('Unable to Refund the payment now, %s', $e instanceof Card ? lcfirst($e->getMessage()) : 'please try again.')
                );
                return $this->redirectToRoute('order_list');
            }
        }

        $redirect = $this->generateUrl('order_list');
        return $this->redirect($redirect);
    }

    /**
     * Show Order create page (checkout page)
     *
     * @param Request $request
     * @param null $code
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, $code = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$request->isMethod('POST')) {
            if ($code === null) {
                $order = $em->getRepository('LocalsBestShopBundle:UserOrder')->getLastOrder($this->getUser());
            } else {
                $order = $em->getRepository('LocalsBestShopBundle:UserOrder')->findOneBy([
                    'code' => $code,
                    'user' => $this->getUser(),
                ]);
            }
            return $this->render('@LocalsBestShop/order/checkout.html.twig', [
                'order' => $order,
                'stripe_public_key' => $this->getParameter('stripe_public_key')
            ]);
        } else {
            $cartData = $request->request->get('sku');
            $cookieName = $this->getParameter('cookie_cart');
            $cart = $request->cookies->get($cookieName);
            $data = json_decode($cart, true);

            foreach ($cartData as $el) {
                $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($el['id']);
                /** @var Combo|Package $productEntity */
                $productEntity = $sku->getProductEntity();
                if ($el['quantity'] > $productEntity->getQuantity()) {
                    $this->addFlash(
                        'danger', 'Unfortunately there are only ' . $productEntity->getQuantity() . ' "' . $sku->getTitle() . '" left in stock. Please change the quantity.');
                    return $this->redirectToRoute('cart_show');
                }
            }

            $splitPercent = $this->getBusiness()->getOwner()->getPaymentSplitSettings();

            $order = new UserOrder();
            $order->setUser($this->getUser());
            $order->setBusiness($this->getBusiness());
            $em->persist($order);

            foreach ($data as $item) {
                $price = $em->getRepository('LocalsBestShopBundle:Price')->find($item['paymentId']);
                /** @var Sku $sku */
                $sku = $price->getSku();

                $orderItem = new OrderItem();

                foreach ($cartData as $cartObj) {
                    if ($cartObj['id'] == $sku->getId()) {
                        $orderItem->setQuantity($cartObj['quantity']);
                        if (isset($cartObj['options'])) {
                            $orderItem->setOptions($cartObj['options']);
                        }
                    }
                }

                $orderItem->setSku($sku);
                $orderItem->setOrder($order);
                $orderItem->setPriceObject($price);
                $orderItem->setMarkup($sku->getMarkup());
                $orderItem->setPrice($price->getretailPrice());
                $orderItem->setTitle($sku->getTitle());
                $orderItem->setRebatePercent($price->getRebate());

                $em->persist($orderItem);
            }

            // Create Hystory for split percentages 
            if ($splitPercent) {
                $splitHystory = new SplitPaymentHystory();
                $splitHystory->setOrderId($order);
                $splitHystory->setCharityPercent($splitPercent->getCharityPercentage());
                $splitHystory->setBusinessPercent($splitPercent->getBusinessPercentage());
                $splitHystory->setEmployeePercent($splitPercent->getManagerEmployeePercentage());
                $splitHystory->setTarget($splitPercent->getTarget());
                $splitHystory->setTargetTwo($splitPercent->getTargetTwo());

                $em->persist($splitHystory);

                // Create history for charities at time of order

                foreach ($splitPercent->getCharities() as $charityHystory) {

                    $manageOrderCharities = new ManageOrderCharities();
                    $manageOrderCharities->setOrderId($order);
                    $manageOrderCharities->setCharityId($charityHystory);
                    $manageOrderCharities->setCharityName($charityHystory->getName());
                    $em->persist($manageOrderCharities);
                }
            }

            $em->flush();

            $response = new Response();
            $cookie = new Cookie(
                $cookieName, json_encode([]), time() + (3600 * 365), '/', null, false, false
            );
            $response->headers->setCookie($cookie);
            $response->send();

            return $this->redirectToRoute('checkout', ['code' => $order->getCode()]);
        }
    }
}
