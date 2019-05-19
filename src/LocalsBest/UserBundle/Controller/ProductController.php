<?php

namespace LocalsBest\UserBundle\Controller;

use DateInterval;
use DateTime;
use LocalsBest\SocialMediaBundle\Entity\Bucket;
use LocalsBest\UserBundle\Entity\ContactUs;
use LocalsBest\UserBundle\Entity\Feedback;
use LocalsBest\UserBundle\Entity\PaymentProfile;
use LocalsBest\UserBundle\Entity\PaymentRow;
use LocalsBest\UserBundle\Entity\PaymentSet;
use LocalsBest\UserBundle\Entity\Product;
use LocalsBest\UserBundle\Entity\ProductImage;
use LocalsBest\UserBundle\Entity\User;
use LocalsBest\UserBundle\Form\ContactUsType;
use LocalsBest\UserBundle\Form\FeedbackType;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Product controller.
 */
class ProductController extends Controller
{
    /**
     * Lists all Product entities.
     * @param Request $request
     *
     * @return array
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentPage = $request->query->get('page', 1);
        $productsListView = $request->cookies->get('products_list_view');

        $orderByArray = [
            'name',
            /*'price',*/
            'date',
//            'popularity',
//            'rating',
        ];
        $orderDirection = $request->query->get('order_direction', 'asc');
        $orderBy = $request->query->get('order_by', $orderByArray[0]);
        $category = $request->query->get('category', null);

        $entities = $em->getRepository('LocalsBestUserBundle:Product')
            ->getAll($this->getUser(), $currentPage, [
                'order_by'  => $orderBy,
                'order_dir' => $orderDirection,
                'category'  => $category,
            ]);

        $limit = 5;
        $maxPages = ceil($entities->count() / $limit);
        $thisPage = $currentPage;

        return array(
            'entities' => $entities,
            'productsListView' => $productsListView,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage,
            'orderDirection' => $orderDirection,
            'orderBy' => $orderBy,
            'orderByArray' => $orderByArray,
            'selectedCategory' => $category,
        );
    }

    /**
     * Lists all Product entities.
     * @param string $businessSlug
     *
     * @return array
     * @Template()
     */
    public function businessShopAction($businessSlug)
    {
        $em = $this->getDoctrine()->getManager();

        $business = null;

        if($businessSlug !== null) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

            if($business === null) {
                $this->addFlash('danger', 'Shop that you looking for does not exist');
                return $this->redirectToRoute('products');
            } else {
                if($this->get('localsbest.checker')->productBySlug('business-shop', $business->getOwner()) == false) {
                    $this->addFlash('danger', 'Shop that you looking for does not exist');
                    return $this->redirectToRoute('products');
                }
            }
        }

        $entities = $em->getRepository('LocalsBestUserBundle:Product')->getAll();

        return array(
            'entities' => $entities,
            'business' => $business,
        );
    }

    /**
     * Finds and displays a Product entity.
     * @param string $slug
     * @param Request $request
     * @return array
     *
     * @Template()
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Product $entity */
        $entity = $em->getRepository('LocalsBestUserBundle:Product')->getOneBySlug($slug);

        if (!$entity[0]) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $feedback = new Feedback();

        $feedback_form = $this->createForm(FeedbackType::class, $feedback, [
            'action' => $this->generateUrl(
                'feedback_create',
                [
                    'productId' => $entity[0]->getId(),
                ]
            ),
            'method' => 'POST',
            'user' => $this->getUser(),
        ]);

        // Update Product views counter

        $views = $entity[0]->getViews();
        $entity[0]->setViews($views + 1);
        $em->flush();

        // Update User Recent Viewed Product List

        $cookieName = $this->getParameter('cookie_recent_viewed');
        $recentViewed = $request->cookies->get($cookieName);

        if ($recentViewed === null) {
            $recentViewed = [];
        } else {
            $recentViewed = json_decode($recentViewed, true);
        }

        // If Recent Viewed List have >= 10 element then remove first

        if(count($recentViewed) >= 10) {
            $recentViewed = array_shift($recentViewed);
        }

        // Add new element to Resent Viewed list

        array_push($recentViewed, $entity[0]->getId());

        // Remove duplicates

        $recentViewed = array_unique($recentViewed);

        $response = new Response();
        $cookie = new Cookie($cookieName, json_encode($recentViewed), time() + (3600*365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        return array(
            'entity'        => $entity,
            'tags'          => $em->getRepository('LocalsBestUserBundle:Tag')->getProductTags($entity[0]->getId()),
            'feedback_form' => $feedback_form->createView(),
        );
    }

    /**
     * Displays User Cart
     * @param Request $request.
     * @return array
     *
     * @Template()
     */
    public function cartAction(Request $request)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);

        if ($cart === null) {
            $products = [];
        } else {
            $data = json_decode($cart, true);

            $em = $this->getDoctrine()->getManager();
            $products = $em->getRepository('LocalsBestUserBundle:ProductType')->getProductsForCart(array_column($data, 'type'));
            foreach ($products as $key => $item) {
                $products[$key]['quantity'] = $data[$item['product']['id']]['quantity'];
            }
        }

        return [
            'products' => $products,
        ];
    }

    /**
     * Add Product to User Cart
     * @param Request $request.
     * @return JsonResponse
     *
     */
    public function cartAddAction(Request $request)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);
        $em = $this->getDoctrine()->getManager();

        $productId = (int)$request->request->get('product');
        $type = (int)$request->request->get('type');
        $quantity = (int)$request->request->get('quantity');
        $parents = $request->request->get('parents', []);

        $types = $em->getRepository('LocalsBestUserBundle:ProductType')->findBy(['id' => $parents]);
        $parents = [];
        foreach ($types as $parentType) {
            $parents[] = $parentType->getProduct()->getId();
        }

        if ($cart === null) {
            $cart = [];
        } else {
            $cart = json_decode($cart, true);
        }

        /** @var Product $neededProduct */
        $neededProduct = $em->getRepository('LocalsBestUserBundle:Product')->find($productId);
        $product = $neededProduct->getParent();

        do {
            if (
                !$this->isProductInCart($product, $cart)
                && $product !== null
                && !in_array($product->getId(), $parents)
            ) {
                return new JsonResponse([
                    'error' => 1,
                    'message' => 'You should buy basic product for "' . $neededProduct->getTitle() . '".',
                    'data' => [],
                ]);
            }
            $product = $product === null ? null : $product->getParent();
        } while ($product !== null);

        $prodType = $em->getRepository('LocalsBestUserBundle:ProductType')->find($type);

        if (($prodType->getPrice()+$prodType->getSetupFee()) == 0) {
            $user = $this->getUser();

            $paymentSet = new PaymentSet();
            $paymentSet->setCreatedBy($user);

            $paymentRow = new PaymentRow();
            $paymentRow->setProductName($prodType->getProduct()->getTitle());
            $paymentRow->setPrice(0);
            $paymentRow->setSetupFee(0);
            $paymentRow->setQuantity(1);

            $paymentRow->setProduct($prodType->getProduct());
            $paymentRow->setProductType($prodType->getType());

            $subResult = null;
            if($prodType->getType() == 'subscription') {
                $endDate = $date = new DateTime('now');
                $endDate->add(new DateInterval('P'.$prodType->getSubscriptionPeriod()*$prodType->getSubscriptionCharges().'M'));

                $paymentRow->setEndDate($endDate);
            }

            if ($prodType->getType() == 'counter') {
                $paymentRow->setProductLimit($prodType->getValue());
                $paymentRow->setProductUses(0);

                $endDate = $date = new DateTime('now');
                $endDate->add(new DateInterval('P'.$prodType->getSubscriptionPeriod().'M'));

                $paymentRow->setEndDate($endDate);
            }

            $paymentRow->setStatus('success');
            $paymentRow->setPaymentSet($paymentSet);
            $em->persist($paymentRow);

            $paymentSet->setAmount(0);

            $em->persist($paymentSet);
            $em->flush();

            return new JsonResponse([
                'error' => 0,
                'message' => 'Product Activated successfully.',
                'data' => [],
            ]);
        }

        $cart[$productId] = ['type' => $type, 'quantity' => $quantity];

        foreach ($types as $parentType) {
            $cart[$parentType->getProduct()->getId()] = ['type' => $parentType->getId(), 'quantity' => $quantity];
        }

        $response = new Response();
        $cookie = new Cookie($cookieName, json_encode($cart), time() + (3600*365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        return new JsonResponse([
            'error' => 0,
            'message' => 'Product added successfully to Your Cart.',
            'data' => [],
        ]);
    }

    /**
     * Remove Product from User Cart
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     *
     */
    public function cartRemoveAction(Request $request, $id)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);
        $productId = (int)$id;
        $em = $this->getDoctrine()->getManager();

        if ($cart === null) {
            $cart = [];
        } else {
            $cart = json_decode($cart, true);
        }

        if(isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        foreach ($cart as $key => $item) {
            $product = $em->getRepository('LocalsBestUserBundle:Product')->find($key);
            $row = [];

            do {
                $row[] = $product->getId();
                $prodId = $product->getId();
                $product = $product->getParent();
            } while ($product !== null && $prodId != $productId);

            if (in_array($productId, $row)) {
                foreach ($row as $el) {
                    if (isset($cart[$el])) {
                        unset($cart[$el]);
                    }
                }
            }
        }

        $response = new Response();
        $cookie = new Cookie($cookieName, json_encode($cart), time() + (3600*365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        return $this->redirectToRoute('products_cart');
    }

    /**
     * Show Checkout page
     * @param Request $request
     *
     * @return array
     * @Template()
     */
    public function checkoutAction(Request $request)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);

        $products = $this->getCartProducts($cart);

        return [
            'products' => $products,
        ];
    }

    private function getCartProducts($cart)
    {
        if ($cart === null) {
            $products = [];
        } else {
            $data = json_decode($cart, true);

            $em = $this->getDoctrine()->getManager();
            $products = $em->getRepository('LocalsBestUserBundle:ProductType')->getProductsForCart(array_column($data, 'type'));
            foreach ($products as $key => $item) {
                $products[$key]['quantity'] = $data[$item['product']['id']]['quantity'];
            }
        }

        return $products;
    }

    public function checkoutPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);

        /** @var User $user */
        $user = $this->getUser();
        $products = $this->getCartProducts($cart);
        $amount = 0;

        $expDate = $data['expiration_year'] . '-' . $data['expiration_month'];

        $paymentSet = new PaymentSet();
        $paymentSet->setCreatedBy($user);

        $customerProfileId = $user->getAuthorizeProfileId();
        $customerPaymentProfile = $em->getRepository('LocalsBestUserBundle:PaymentProfile')->findOneBy([
            'user' => $user,
            'last4' => substr(str_replace(' ', '', $data['credit_card_number']), -4),
            'expirationDate' => $expDate,
        ]);

        $customerPaymentProfileId = $customerPaymentProfile !== null
            ? $customerPaymentProfile->getPaymentProfileId()
            : null;

        if ($customerProfileId === null) {
            $result = $this->authorizeCreateProfile($user, $data);

            if(is_array($result)) {
                $customerProfileId = $result['customerProfile'];
                $customerPaymentProfileId = $result['customerPaymentProfile'];

                $user->setAuthorizeProfileId($customerProfileId);

                $paymentProfile = new PaymentProfile();
                $paymentProfile->setUser($user);
                $paymentProfile->setPaymentProfileId($customerPaymentProfileId);
                $paymentProfile->setLast4(substr(str_replace(' ', '', $data['credit_card_number']), -4));
                $paymentProfile->setExpirationDate($expDate);

                $em->persist($paymentProfile);
                $em->flush();
            } else {
                $this->addFlash('danger', $result);
            }
        }

        if($customerPaymentProfileId === null) {
            $result = $this->authorizeCreatePaymentProfile($user, $data, $customerProfileId);

            $customerPaymentProfileId = $result['customerPaymentProfileId'];

            $paymentProfile = new PaymentProfile();
            $paymentProfile->setUser($user);
            $paymentProfile->setPaymentProfileId($customerPaymentProfileId);
            $paymentProfile->setLast4(substr(str_replace(' ', '', $data['credit_card_number']), -4));
            $paymentProfile->setExpirationDate($expDate);

            $em->persist($paymentProfile);
            $em->flush();
        }

        /** @var Product $product */
        foreach ($products as $product) {
            $prod_desc = $product['product']['title'] . '-' . $product['quantity'] . ';';

            $result = $this->authorizeSinglePayment(
                $customerProfileId,
                $customerPaymentProfileId,
                $prod_desc,
                ($product['setupFee'] + $product['price'] * (1 + $product['margin'] / 100))
            );

            $paymentRow = new PaymentRow();
            $paymentRow->setProductName($product['product']['title']);
            $paymentRow->setPrice($product['price'] * (1 + $product['margin'] / 100));
            $paymentRow->setSetupFee($product['setupFee']);
            $paymentRow->setQuantity($product['quantity']);

            $paymentRow->setProduct($em->getReference('LocalsBest\UserBundle\Entity\Product', $product['product']['id']));
            $paymentRow->setProductType($product['type']);

            $subResult = null;
            if($product['type'] == 'subscription') {
                $subResult = $this->authorizeRecurringBilling($product, $customerProfileId, $customerPaymentProfileId);

                $endDate = $date = new DateTime('now');
                $endDate->add(new DateInterval('P' . $product['subscriptionPeriod'] * $product['subscriptionCharges'] . 'M'));

                $paymentRow->setEndDate($endDate);
            }

            if($product['type'] == 'counter') {
                $paymentRow->setProductLimit($product['value']);
                $paymentRow->setProductUses(0);

                $endDate = $date = new DateTime('now');
                $endDate->add(new DateInterval('P' . $product['subscriptionPeriod'] . 'M'));

                $paymentRow->setEndDate($endDate);
            }

            if (is_array($result)) {
                $amount += $product['setupFee'] + $product['quantity'] * ($product['price'] * (1 + $product['margin'] / 100));
                if($subResult !== null && is_array($subResult)) {
                    $paymentRow->setPaymentServiceId($result['transId'] . '|' . $subResult['SubscriptionId']);
                } else {
                    $paymentRow->setPaymentServiceId($result['transId']);
                }
                $result = $result['status'];
            } else {
                $this->addFlash(
                    'danger',
                    "Some problems with payment system, product '" . $product['product']['title'] . "' was not paid."
                );
            }

            /** @var Bucket $bucket */
            $bucket = $em->getRepository('LocalsBestSocialMediaBundle:Bucket')->findOneBy([
                'product' => $em->getReference('LocalsBestUserBundle:Product', $product['product']['id'])
            ]);

            if ($bucket !== null) {
                $bucket->addBuyer($user);
                $user->addBoughtBucket($bucket);
            }

            $paymentRow->setStatus($result);
            $paymentRow->setPaymentSet($paymentSet);
            $em->persist($paymentRow);
        }
        $paymentSet->setAmount($amount);

        $em->persist($paymentSet);
        $em->flush();

        $response = new Response();
        $cookie = new Cookie($cookieName, json_encode([]), time() + (3600*365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        $this->addFlash('success', "Payment process was finished.");
        return $this->redirectToRoute('products');
    }

    /**
     * Generate Block with popular Product (top viewed)
     *
     * @return array
     * @Template()
     */
    public function popularAction($productLimit = 5, $businessSlug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = null;
        $business = null;

        if($businessSlug !== null) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

            if($business === null) {
                $this->addFlash('danger', 'Shop that you looking for does not exist');
                return $this->redirectToRoute('products');
            } else {
                if($this->get('localsbest.checker')->productBySlug('business-shop', $business->getOwner()) == false) {
                    $this->addFlash('danger', 'Shop that you looking for does not exist');
                    return $this->redirectToRoute('products');
                } else {
                    $entities = [];
                }
            }
        }

        if($entities === null) {
            $entities = $em->getRepository('LocalsBestUserBundle:Product')->getPopular($productLimit);
        }

        return [
            'products' => $entities,
        ];
    }

    /**
     * Generate Block with recent reviews of Products
     * @param int $productLimit
     * @param string $businessSlug
     *
     * @return array
     * @Template()
     */
    public function recentReviewsAction($productLimit = 5, $businessSlug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = null;
        $business = null;

        if($businessSlug !== null) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

            if($business === null) {
                $this->addFlash('danger', 'Shop that you looking for does not exist');
                return $this->redirectToRoute('products');
            } else {
                if($this->get('localsbest.checker')->productBySlug('business-shop', $business->getOwner()) == false) {
                    $this->addFlash('danger', 'Shop that you looking for does not exist');
                    return $this->redirectToRoute('products');
                } else {
                    $entities = [];
                }
            }
        }

        if($entities === null) {
            $entities = $em->getRepository('LocalsBestUserBundle:Product')->getByRecentReviews($productLimit);
        }

        return [
            'feedbacks' => $entities,
        ];
    }

    /**
     * Generate Block with recent reviews of Products
     * @param Request $request
     * @param int $productLimit
     *
     * @return array
     * @Template()
     */
    public function recentViewedAction(Request $request, $productLimit = 5)
    {
        $cookieName = $this->getParameter('cookie_recent_viewed');
        $recentViewed = $request->cookies->get($cookieName);

        if ($recentViewed === null) {
            $products = [];
        } else {
            $data = json_decode($recentViewed, true);

            $em = $this->getDoctrine()->getManager();
            $products = $em->getRepository('LocalsBestUserBundle:Product')->findBy(['id' => $data], null, $productLimit);
        }

        return [
            'products' => $products,
        ];
    }

    /**
     * Generate Block with related Products
     * @param Product $product
     * @param int $productLimit
     *
     * @return array
     * @Template()
     */
    public function relatedAction($product, $productLimit = 3)
    {
        $em = $this->getDoctrine()->getManager();
        if ($product->getCategories()->count() > 0) {
            $products = $em->getRepository('LocalsBestUserBundle:Product')->getRelated($product, $productLimit);
        } else {
            $products = [];
        }

        return [
            'products' => $products,
        ];
    }

    /**
     * Toggle Products view (blocks/list)
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function toggleProductsViewAction(Request $request)
    {
        $productsListView = $request->cookies->get('products_list_view');

        if ($productsListView === null) {
            $productsListView = 1;
        } else {
            $productsListView = !$productsListView;
        }

        $response = new Response();
        $cookie = new Cookie('products_list_view', $productsListView, time() + (3600*365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        return $this->redirect($request->headers->get('referer'));
    }

    public function imageDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ProductImage $image */
        $image = $em->getRepository('LocalsBestUserBundle:ProductImage')->find($id);

        $product = $image->getProduct();

        if($image === null) {
            return new JsonResponse([
                'error' => 1,
                'message' => 'Image doesn\'t exist.',
            ]);
        }

        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($image, 'imageFile');

        if(file_exists($this->get('kernel')->getRootDir() . '/../web' . $path)) {
            unlink($this->get('kernel')->getRootDir() . '/../web' . $path);
        }

        $product->removeImage($image);
        $em->remove($image);
        $em->flush();

        if($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'error' => 0,
                'message' => 'Product Image Successfully deleted.',
            ]);
        } else {
            $referrer = $request->headers->get('referer');
            return $this->redirect($referrer);
        }

    }

    /**
     * Generate Block with Products Categories
     * @param Request $request
     * @param string $url
     * @param int $selected
     *
     * @return array
     * @Template()
     */
    public function categoriesListAction(Request $request, $url, $selected)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('LocalsBestUserBundle:ProductCategory')->findBy([], ['title' => 'ASC']);

        return [
            'categories' => $categories,
            'url' => $url,
            'selected' => $selected,
        ];
    }

    /**
     * Display page with users orders
     *
     * @return array
     * @Template()
     */
    public function ordersAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $paymentSets = $em->getRepository('LocalsBestUserBundle:PaymentSet')->findBy(['createdBy' => $user], ['createdAt' => 'DESC']);

        return [
            'paymentSets' => $paymentSets,
        ];
    }

    /**
     * Display orders products
     *
     * @param int $id
     * @return array
     * @Template()
     */
    public function orderDetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var PaymentSet $paymentSet */
        $paymentSet = $em->getRepository('LocalsBestUserBundle:PaymentSet')->findOneBy(
            [
                'createdBy' => $user,
                'id'        => $id,
            ]
        );

        $paymentRows = $paymentSet->getPaymentRows();

        return [
            'paymentRows' => $paymentRows,
        ];
    }

    /**
     * Show product parent block
     *
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function parentProductBlockAction(Request $request, $product)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);
        $products = [];

        do {
            if ($product !== null) {
                if (!$this->isProductInCart($product, $cart)) {
                    $products[] = $product;
                }
                $product = $product->getParent();
            }
        } while ($product !== null);

        return $this->render(
            '@LocalsBestUser/product/parentProductBlock.html.twig',
            ['products' => $products]
        );
    }

    /**
     * Check product in user cart
     *
     * @param Product $product
     * @param string|array $cart
     * @return bool
     */
    private function isProductInCart($product, $cart)
    {
        $result = false;

        if ($cart === null) {
            $cart = [];
        } else {
            if(!is_array($cart)) {
                $cart = json_decode($cart, true);
            }
        }

        if ($product !== null && in_array($product->getId(), array_keys($cart))) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param $profileId
     * @param $paymentProfileId
     * @param $productDescription
     * @param $cost
     * @return array|string
     */
    private function authorizeSinglePayment($profileId, $paymentProfileId, $productDescription, $cost)
    {
        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getParameter('auth_login_id'));
        $merchantAuthentication->setTransactionKey($this->getParameter('auth_transaction_key'));
        $refId = 'ref' . time();

        $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
        $profileToCharge->setCustomerProfileId($profileId);
        $paymentProfile = new AnetAPI\PaymentProfileType();
        $paymentProfile->setPaymentProfileId($paymentProfileId);
        $profileToCharge->setPaymentProfile($paymentProfile);

        $order = new AnetAPI\OrderType();
        $order->setDescription($productDescription);

        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($cost);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setProfile($profileToCharge);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse( ANetEnvironment::PRODUCTION);

        if ($response != null) {
            if($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    return [
                        'status' => 'success',
                        'transId' => $tresponse->getTransId(),
                    ];
                } else {
                    return 'fail';
                }
            } else {
                return 'fail';
            }
        } else {
            return 'fail';
        }
    }

    /**
     * Create CustomerProfileID for Authorize
     *
     * @param User $user
     * @param array $cardInfo
     * @return array
     */
    private function authorizeCreateProfile(User $user, array $cardInfo)
    {
        if (!defined('AUTHORIZENET_LOG_FILE')) {
            define("AUTHORIZENET_LOG_FILE", '../app/logs/payment_copy.log');
        }

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getParameter('auth_login_id'));
        $merchantAuthentication->setTransactionKey($this->getParameter('auth_transaction_key'));
        $refId = 'ref' . time();

        $expDate = $cardInfo['expiration_year'] . '-' . $cardInfo['expiration_month'];

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber(str_replace(' ', '', $cardInfo['credit_card_number']));
        $creditCard->setExpirationDate($expDate);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);

        // Create the Bill To info
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($user->getFirstName());
        $billTo->setLastName($user->getLastName());
        $billTo->setCompany($user->getBusinesses()->first()->getName());
        $billTo->setCountry("USA");

        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();

        $paymentProfile->setCustomerType('individual');
        $paymentProfile->setBillTo($billTo);
        $paymentProfile->setPayment($paymentCreditCard);
        $paymentProfiles[] = $paymentProfile;
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setDescription("EasyCloses Customer Profile");

        $customerProfile->setMerchantCustomerId("M_" . $user->getId());
        $customerProfile->setEmail($user->getPrimaryEmail()->getEmail());
        $customerProfile->setPaymentProfiles($paymentProfiles);

        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setProfile($customerProfile);
        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
            return [
                'customerProfile' => $response->getCustomerProfileId(),
                'customerPaymentProfile' => $response->getCustomerPaymentProfileIdList()[0],
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            return $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText();
        }
    }

    /**
     * Create CustomerPaymentProfileID for Authorize
     *
     * @param User $user
     * @param array $cardInfo
     * @param $existingCustomerProfileId
     * @return array|string
     */
    private function authorizeCreatePaymentProfile(User $user, array $cardInfo, $existingCustomerProfileId)
    {
        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getParameter('auth_login_id'));
        $merchantAuthentication->setTransactionKey($this->getParameter('auth_transaction_key'));

        $expDate = $cardInfo['expiration_year'] . '-' . $cardInfo['expiration_month'];

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber(str_replace(' ', '', $cardInfo['credit_card_number']));
        $creditCard->setExpirationDate($expDate);
        $creditCard->setCardCode($cardInfo['cvv2']);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);

        // Create the Bill To info for new payment type
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($user->getFirstName());
        $billTo->setLastName($user->getLastName());
        $billTo->setCompany($user->getBusinesses()->first()->getName());
        $billTo->setPhoneNumber($user->getPrimaryPhone()->getNumber());
        $billTo->setAddress('Address');
        $billTo->setCity('City');
        $billTo->setState('AA');
        $billTo->setZip('12345');
        $billTo->setCountry("USA");
        $billTo->setFaxNumber("");

        // Create a new Customer Payment Profile
        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setCustomerType('individual');
        $paymentProfile->setBillTo($billTo);
        $paymentProfile->setPayment($paymentCreditCard);
//        $paymentProfile->setDefaultPaymentProfile(true);

        $paymentProfiles[] = $paymentProfile;

        // Submit a CreateCustomerPaymentProfileRequest to create a new Customer Payment Profile
        $paymentProfileRequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $paymentProfileRequest->setMerchantAuthentication($merchantAuthentication);
        //Use an existing profile id
        $paymentProfileRequest->setCustomerProfileId( $existingCustomerProfileId );
        $paymentProfileRequest->setPaymentProfile( $paymentProfile );
        $paymentProfileRequest->setValidationMode("liveMode");

        $controller = new AnetController\CreateCustomerPaymentProfileController($paymentProfileRequest);
        $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
            return [
                'customerPaymentProfileId' => $response->getCustomerPaymentProfileId(),
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            return $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText();
        }
    }

    private function authorizeRecurringBilling($productType, $customerProfileId, $customerPaymentProfileId)
    {
        // Common Set Up for API Credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getParameter('auth_login_id'));
        $merchantAuthentication->setTransactionKey($this->getParameter('auth_transaction_key'));
        $refId = 'ref' . time();

        // Subscription Type Info
        $subscription = new AnetAPI\ARBSubscriptionType();
        $subscription->setName($productType['id'] . " " . $productType['product']['title']);

        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength($productType['subscriptionPeriod']);
        $interval->setUnit("months");

        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);

        $startDate = $date = new DateTime('now');
        $startDate->add(new DateInterval('P' . $productType['subscriptionPeriod'] . 'M'));

        $paymentSchedule->setStartDate($startDate);
        $paymentSchedule->setTotalOccurrences(($productType['subscriptionCharges'] - 1));
        $paymentSchedule->setTrialOccurrences("0");

        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setAmount($productType['price'] * (1 + $productType['margin'] / 100));
        $subscription->setTrialAmount("0.00");

        $profile = new AnetAPI\CustomerProfileIdType();
        $profile->setCustomerProfileId($customerProfileId);
        $profile->setCustomerPaymentProfileId($customerPaymentProfileId);
//        $profile->setCustomerAddressId($customerAddressId);

        $subscription->setProfile($profile);

        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new AnetController\ARBCreateSubscriptionController($request);

        $response = $controller->executeWithApiResponse( ANetEnvironment::PRODUCTION);

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
            $result = [
                'SubscriptionId' => $response->getSubscriptionId(),
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            $result =  $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText();
        }

        return $result;
    }

    /**
     * Display Contact Us Page with form
     *
     * @param Request $request
     * @return array|RedirectResponse
     * @Template("@LocalsBestUser/product/contact-us.html.twig")
     */
    public function contactUsAction(Request $request)
    {
        // create Doctrine Entity Manager
        $em = $this->getDoctrine()->getManager();
        // check product ID in request
        $productId = $request->query->get('product');
        // create new Entity of Contact Us
        $contactUs = new ContactUs();

        if ($productId !== null) {
            // if is set product ID, get product Entity
            $product = $em->getRepository('LocalsBestUserBundle:Product')->find($productId);
            // and set it to Contact Us Entity
            $contactUs->setProduct($product);
        }

        if (null !== $user = $this->getUser()) {
            // if user is logged in set his information
            $contactUs->setUserName($user->getFullName());
            $contactUs->setEmail($user->getPrimaryEmail()->getEmail());
            $contactUs->setPhone($user->getPrimaryPhone()->getNumber());
        }
        // create Contact Us Form object
        $form = $this->createForm(ContactUsType::class, $contactUs);
        // add submit button to Form
        $form->add('submit', Type\SubmitType::class, ['label'=>'Send', 'attr'=>['class'=>'btn btn-primary']]);

        // if Form was submitted
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            // if Form is valid
            if ($form->isValid()) {
                // save Contact Us Entity
                $em->persist($contactUs);
                $em->flush();

                $this->get('localsbest.mailman')->sendContactUsMail($contactUs);

                // show success message
                $this->addFlash('success', 'Thank you! You will be contacted within 1 business day');
                // choose redirect url
                if ($contactUs->getProduct() !== null) {
                    return $this->redirectToRoute('products_show', ['slug' => $contactUs->getProduct()->getSlug()]);
                } else {
                    return $this->redirectToRoute('contact_us');
                }
            }
        }
        // Send parameters to view
        return [
            'form' => $form->createView(),
        ];
    }
}
