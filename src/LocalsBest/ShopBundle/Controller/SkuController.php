<?php

namespace LocalsBest\ShopBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Combo;
use LocalsBest\ShopBundle\Entity\Comment;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\Package;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\SkuContactUs;
use LocalsBest\ShopBundle\Form\CommentType;
use LocalsBest\ShopBundle\Form\SkuContactUsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SkuController extends Controller
{
    /**
     * List of products
     *
     * @param Request $request
     *
     * @return array
     * @throws QueryException
     *
     * @Template
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $currentPage = $request->query->get('page', 1);
        $productsListView = $request->cookies->get('products_list_view');

        $orderByArray = [
            'title',
            'date'
        ];

        $orderDirection = $request->query->get('order_direction', 'asc');
        $orderBy = $request->query->get('order_by', array_first($orderByArray));
        $category = $request->query->get('category', null);
        $search = $request->query->get('package', null);
        $limit = 10;

        $entitiesCount = $em->getRepository('LocalsBestShopBundle:Sku')->getCount(false, [
            'category' => $category,
            'business' => $this->getBusiness(),
            'user' => $this->getUser(),
            'search' => $search
        ]);

        $maxPages = ceil($entitiesCount / $limit);

        if ($currentPage > $maxPages && $maxPages > 0) {
            $currentPage = $maxPages;
        }

        $thisPage = $currentPage;

        $entities = $em->getRepository('LocalsBestShopBundle:Sku')->findAllBy(false, [
            'business' => $this->getBusiness(),
            'user' => $this->getUser(),
            'category' => $category,
            'orderDir' => $orderDirection,
            'orderBy' => $orderBy,
            'limit' => $limit,
            'offset' => ($currentPage - 1) * $limit,
            'search' => $search
        ]);

        return array(
            'business' => $this->getBusiness(),
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
     * Finds and displays a Product entity.
     *
     * @param integer $id
     * @param Request $request
     *
     * @throws NonUniqueResultException
     * @return array
     *
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('LocalsBestShopBundle:Sku')->findAllBy(false, [
            'business' => $this->getBusiness(),
            'user' => $this->getUser(),
        ]);

        $array_sku = [];
        foreach ($entities as $entity) {
            array_push($array_sku, $entity->getId());
        }
        //check for the custom packages
        $entities = $em->getRepository('LocalsBestShopBundle:Package')->findBy(['type' => 7, 'assignee' => $this->getUser()]);

        foreach ($entities as $entity) {
            array_push($array_sku, $entity->getSku()->getId());
        }

        $status_view = 0;
        if (!in_array($id, $array_sku)) {
            $status_view = 1;
        }
        /** @var Sku $entity */
        $entity = $em->getRepository('LocalsBestShopBundle:Sku')->getOne($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        // Update Product views counter
        $views = $entity->getViews();
        $entity->setViews($views + 1);
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
        if (count($recentViewed) >= 10) {
            $recentViewed = array_shift($recentViewed);
        }

        $comment = new Comment();
        $comment->setCreatedBy($this->getUser());

        $commentForm = $this->createForm(CommentType::class, $comment, [
            'sku' => $entity,
            'action' => $this->generateUrl(
                'comment_create',
                [
                    'skuId' => $entity->getId(),
                ]
            ),
            'method' => 'POST',
        ]);

        // Add new element to Resent Viewed list
        array_push($recentViewed, $entity->getId());

        // Remove duplicates
        $recentViewed = array_unique($recentViewed);
        $result = [];
        foreach ($entity->getPackage()->getSets() as $itemset) {
            foreach ($itemset->getItem()->getImages() as $image) {
                $split = explode("ec_shop", $image->getFile());
                $split = $this->getParameter('temp_download_aws_folder') . 'ec_shop' . $split[2];
                array_push($result, $split);
            }
        }
        $response = new Response();
        $cookie = new Cookie($cookieName, json_encode($recentViewed), time() + (3600 * 365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        //get the custom quote status of the current package vendor

        $quotes = $entity->getPackage()->getCreatedBy()->getCustomQuotes();
        $entities_quotes = $em->getRepository('LocalsBestShopBundle:Quotes')->findBy(['userid' => $this->getUser(), 'packageId' => $entity->getPackage()->getId()]);

        if (count($entities_quotes) == 1) {
            $status = 0;
        } else {
            if (empty($entities_quotes)) {
                $status = 1;
            } else {
                $status = 0;
            }
        }

        foreach ($entity->getPackage()->getCreatedBy()->getOwner()->getAboutMe() as $about) {
            $aboutme = $about->getAboutMe();
        }

        if ($status_view) {
            return array(
                'business' => $this->getBusiness(),
                'entity' => $entity,
                'tags' => $entity->getTags(),
                'comment_form' => $commentForm->createView(),
                'result' => $result,
                'success' => false,
                'quotes' => $quotes,
                'status' => $status,
                'aboutme' => $aboutme
            );
        } else {
            return array(
                'business' => $this->getBusiness(),
                'entity' => $entity,
                'tags' => $entity->getTags(),
                'comment_form' => $commentForm->createView(),
                'result' => $result,
                'success' => true,
                'quotes' => $quotes,
                'status' => $status,
                'aboutme' => $aboutme
            );
        }
    }

    /**
     * Lists of non approved Sku entities.
     *
     * @return array
     *
     * @Template
     */
    public function forApproveListAction()
    {
        if ($this->getUser()->getRole()->getLevel() > 1) {
            throw $this->createNotFoundException('Unable to find page.');
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LocalsBestShopBundle:Sku')->findAllBy(true);
      
        return $this->render('@LocalsBestShop/sku/forApproveList.html.twig', array(
            'entities' => $entities
        )
        );
    }

    /**
     * Lists of Sku for Business Able functionality.
     *
     * @return array
     *
     * @Template("@LocalsBestShop/sku/business-able.html.twig")
     */
    public function businessAbleAction(Request $request)
    {

        if ($this->getUser()->getRole()->getLevel() != 4) {
            throw $this->createNotFoundException('Unable to find page.');
        }

        $em = $this->getDoctrine()->getManager();
        $business = $this->getBusiness();

        return array(
            'business' => $business,
            'user' => $this->getUser(),
            'params' => http_build_query($request->query->all()),
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function changeShopSkuStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if ($user->getOwner() === null) {
            throw $this->createAccessDeniedException();
        }

        $business = $this->getBusiness();
        $data = $request->request->all();

        // save status for custom quotes in database table business
        if (isset($data['customQuote'])) {
            if ($data['customQuote'] == 'on') {
                $data['customQuote'] = 1;
            } else {
                $data['customQuote'] = 0;
            }
            $business->setCustomQuotes($data['customQuote']);
        }
        // end of save status for custom quotes in database table business

        $business->setShopSkuStatus($data['shopProductsStatus']);

        $em->flush();

        $this->addFlash('success', 'Shop settings save successfully.');
        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function toggleDisableSkuAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user->getOwner() === null) {
            throw $this->createAccessDeniedException();
        }

        $business = $this->getBusiness();
        $data = $request->request->all();

        $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($data['skuId']);

        if (!$business->getSkuDisable()->contains($sku)) {
            $business->addSkuDisable($sku);
            $sku->addDisableForBusiness($business);
        } else {
            $business->removeSkuDisable($sku);
            $sku->removeDisableForBusiness($business);
        }
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'result' => 'success'
            ]);
        }
        die('not ajax');
    }

    /**
     * Change status for SKU
     *
     * @param Request $request
     *
     * @return Response|static
     */
    public function statusChangeAction(Request $request)
    {
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        /** @var Sku $sku */
        $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($data['sku']);

        /** @var Package|Combo $object */
        if ($sku->getPackage() !== null) {
            $object = $sku->getPackage();
        } else {
            $object = $sku->getCombo();
        }

        $object->setStatus($data['status']);

        /** @var Item $item */
        foreach ($sku->getItems() as $item) {
            foreach ($item->getRestrictions() as $restriction) {
                if (!$sku->getRestrictions()->contains($restriction)) {
                    $sku->addRestriction($restriction);
                    $restriction->addSku($sku);
                }
            }
        }

        $em->flush();

        $businesses = $em->getRepository('LocalsBestUserBundle:Business')->findBy(['shopSkuStatus' => 'need_manager_approve']);

        foreach ($businesses as $business) {
            $business->addSkuDisable($sku);
            $sku->addDisableForBusiness($business);
        }

        $em->flush();

        if ($sku->getPackage() !== null) {
            $object = $sku->getPackage();
            //send notification to Vender for status change of package
            $this->createStatusNotification($data, $object);
        }

        return JsonResponse::create([
                'success' => true,
        ]);
    }

    /**
     * Add notification for vendor about status update
     *
     * @param array $data
     *
     * @return bool
     */
    private function createStatusNotification($data, $object)
    {
        // Notification to vender that package is approved for not
        if ($data['status'] == 2) {
            $status = "Published";
        } elseif ($data['status'] == 5) {
            $status = "Disapproved";
        } else {
            $status = "Pending for Approval";
        }

        $message = 'Package Id #' . $object->getTitle() . ' has been ' . $status;
        // Send Notification about this
        $this->get('localsbest.notification')
            ->addNotification(
                $message,
                'packages_show',
                array('id' => $object->getId()),
                array($object->getCreatedBy()),
                array($object->getCreatedBy())
            )
        ;

        return true;
    }

    /**
     * Generate Block with related SKUs
     *
     * @param Sku $sku
     * @param int $productLimit
     *
     * @return array
     * @Template()
     */
    public function relatedAction($sku, $productLimit = 3)
    {
        $em = $this->getDoctrine()->getManager();

        if ($sku->getCategories()->count() > 0) {
            $skus = $em->getRepository('LocalsBestShopBundle:Sku')
                ->getRelated($sku, $productLimit, $this->getUser(), $this->getBusiness());
        } else {
            $skus = [];
        }

        return [
            'products' => $skus,
        ];
    }

    /**
     * Generate Block with recent reviews of SKUs
     *
     * @param Request $request
     * @param int $productLimit
     * @param string $businessSlug
     *
     * @return array|RedirectResponse
     *
     * @Template()
     */
    public function recentViewedAction(Request $request, $productLimit = 5, $businessSlug = null)
    {
        $em = $this->getDoctrine()->getManager();
        $cookieName = $this->getParameter('cookie_recent_viewed');
        $recentViewed = $request->cookies->get($cookieName);

        if ($businessSlug !== null) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

            if ($business === null) {
                $this->addFlash('danger', 'Shop that you looking for does not exist');
                return $this->redirectToRoute('ec_shop_main_page');
            }
        } else {
            $business = $this->getBusiness();
        }

        if ($recentViewed === null) {
            $skus = [];
        } else {
            $data = json_decode($recentViewed, true);

            $em = $this->getDoctrine()->getManager();
            $skus = $em->getRepository('LocalsBestShopBundle:Sku')->getRecentViewed($data, $productLimit, $business, $this->getUser());
        }

        return [
            'products' => $skus,
        ];
    }

    /**
     * Generate Block with popular SKUs (top viewed)
     *
     * @param int $productLimit
     * @param string $businessSlug
     *
     * @return array|RedirectResponse
     * @Template()
     */
    public function popularAction($productLimit = 5, $businessSlug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = null;
        $business = null;

        if ($businessSlug !== null) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

            if ($business === null) {
                $this->addFlash('danger', 'Shop that you looking for does not exist');
                return $this->redirectToRoute('ec_shop_main_page');
            }
        }

        if ($entities === null) {
            $entities = $em->getRepository('LocalsBestShopBundle:Sku')->getPopular($productLimit, $business, [
                'business' => $this->getBusiness(),
                'user' => $this->getUser(),
                'limit' => $productLimit,
                ]
            );
        }

        return [
            'products' => $entities,
        ];
    }

    /**
     * Generate Block with recent reviews of SKUs
     *
     * @param int $productLimit
     * @param string $businessSlug
     *
     * @return array|RedirectResponse
     *
     * @Template()
     */
    public function recentReviewsAction($productLimit = 5, $businessSlug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = null;
        $business = null;

        if ($businessSlug !== null) {
            $business = $em->getRepository('LocalsBestUserBundle:Business')->findOneBy(['slug' => $businessSlug]);

            if ($business === null) {
                $this->addFlash('danger', 'Shop that you looking for does not exist');
                return $this->redirectToRoute('ec_shop_main_page');
            } else {
                if ($this->get('localsbest.checker')->productBySlug('business-shop', $business->getOwner()) == false) {
                    $this->addFlash('danger', 'Shop that you looking for does not exist');
                    return $this->redirectToRoute('ec_shop_main_page');
                } else {
                    $entities = [];
                }
            }
        }

        if ($entities === null) {
            $entities = $em->getRepository('LocalsBestShopBundle:Sku')->getByRecentReviews($productLimit);
        }

        return [
            'feedbacks' => $entities,
        ];
    }

    /**
     * Display Contact Us Page with form
     *
     * @Template("@LocalsBestShop/sku/contact-us.html.twig")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function contactUsAction(Request $request)
    {
        // create Doctrine Entity Manager
        $em = $this->getDoctrine()->getManager();
        // check product ID in request
        $productId = $request->query->get('product');
        // create new Entity of Contact Us
        $contactUs = new SkuContactUs();

        if ($productId !== null) {
            // if is set product ID, get sku Entity
            $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($productId);
            // and set it to Contact Us Entity
            $contactUs->setSku($sku);
            $contactUs->setServiceName($sku->getPackage()->getTitle());
        }

        if (null !== $user = $this->getUser()) {
            // if user is logged in set his information
            $contactUs->setUserName($user->getFullName());
            $contactUs->setEmail($user->getPrimaryEmail()->getEmail());
            $contactUs->setPhone($user->getPrimaryPhone()->getNumber());
        }

        // create Contact Us Form object
        $form = $this->createForm(SkuContactUsType::class, $contactUs);
        // add submit button to Form
        $form->add('submit', Type\SubmitType::class, ['label' => 'Send', 'attr' => ['class' => 'btn btn-primary']]);

        // if Form was submitted
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            // if Form is valid
            if ($form->isValid()) {
                // save Contact Us Entity
                $contactUs->setIsRead(0);
                $em->persist($contactUs);
                $em->flush();

                $senderEmail[] = $sku->getPackage()->getCreatedBy()->getPrimaryEmail()->getEmail();
                $phoneNumber = $sku->getPackage()->getCreatedBy()->getPrimaryPhone()->getNumber();

                $this->get('localsbest.mailman')->sendSkuContactUsMail($contactUs, $senderEmail);

                // Send text message
                $number = str_replace(['-', ' ', '(', ')', '.'], '', $phoneNumber);
                $message = 'New Contact Us Requested with note ' . $contactUs->getNote() . ' by user ' . $user->getFullName() . ' For further details visit your email';
                $sender = $this->get('jhg_nexmo_sms');
                $sender->sendText('+1' . $number, $message, null);

                // Send EC notification
                $message = 'Contact Us : ' . $contactUs->getNote();
                $this->get('localsbest.notification')
                    ->addNotification(
                        $message, 'vendor_contact_request', [], array($sku->getPackage()->getCreatedBy()), []);


                // show success message
                $this->addFlash('success', 'Thank you! You will be contacted within 1 business day');
                // choose redirect url
                if ($contactUs->getSku() !== null) {
                    return $this->redirectToRoute('sku_show', ['id' => $contactUs->getSku()->getId()]);
                } else {
                    return $this->redirectToRoute('sku_contact_us');
                }
            }
        }
        // Send parameters to view
        return [
            'form' => $form->createView(),
        ];
    }

    public function businessAjaxAction(Request $request)
    {
        $params = $request->query->all();

        $em = $this->getDoctrine()->getManager();
        $business = $this->getBusiness();

        $entities = $em->getRepository('LocalsBestShopBundle:Sku')->findAllForBusinessApprove($business, $this->getUser(), $params);
        $entities_length = $em->getRepository('LocalsBestShopBundle:Sku')->getLength($business, $this->getUser(), $params);

        $result = [];
        $result = [
            'draw' => (int) $params['draw'],
            'data' => '',
            'recordsTotal' => count($entities_length),
            'recordsFiltered' => count($entities_length),
        ];

        foreach ($entities as $entity) {
            if (count($entity->getCategories()) > 0) {
                foreach ($entity->getCategories() as $category) {
                    $category = $category->getTitle();
                }
            } else {
                $category = '-';
            }
            foreach ($entity->getPrices() as $price) {
                $rebate = $price->getRebate();
            }
            $tag = '';
            $counter = 0;
            foreach ($entity->getTags() as $tags) {
                $counter++;
                $tag .= $tags->getName();
                if ($counter != count($entity->getTags()))
                    $tag .= ',';
            }
            if (!$business->isDisabledSku($entity)) {
                $status = 'checked';
            } else
                $status = '';

            $checkbox = '<input type="hidden" name="product[' . $entity->getId() . ']" value="0">
                        <input ' . $status . ' class="disableProduct" type="checkbox" data-product="' . $entity->getId() . '" name="product[' . $entity->getId() . ']" value="1">';


            $data[] = [
                $checkbox,
                $entity->getPackage() ? $entity->getPackage()->getCreatedBy()->getOwner()->getName() : '-',
                $entity->getPackage() ? $entity->getPackage()->getTitle() : '-',
                $entity->getPackage() ? $entity->getPackage()->getIndustryType() ? $entity->getPackage()->getIndustryType()->getName() : '-' : '-',
                $category,
                $rebate,
                $entity->getPackage() ? $entity->getPackage()->getCreatedAt()->format('m/d/Y H:i:s') : '-',
                $tag
            ];
        }


        $result['data'] = $data;
        return new Response(json_encode($result));
    }

    public function changeQuoteStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $business = $this->getBusiness();

        $data = $request->request->all();

        // save status for custom quotes in database table business
        if (isset($data['customQuote'])) {
            if ($data['customQuote'] == 'on') {
                $data['customQuote'] = 1;
            }
        } else {
            $data['customQuote'] = 0;
        }
        $user->setCustomQuotes($data['customQuote']);
        // end of save status for custom quotes in database table business

        $em->flush();

        $this->addFlash('success', 'Shop settings save successfully.');
        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer);
    }

    public function showContactRequestAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $contactUsRequests = $em->getRepository('LocalsBestShopBundle:SkuContactUs')->findAllVendorRequests($this->getUser());
        $contactUsRequest = $contactUsRequests;
        // also update read status of requests
        foreach ($contactUsRequest as $contactUs) {
            $contactUs->setIsRead(1);
            $em->persist($contactUs);
            $em->flush();
        }

        return $this->render('@LocalsBestShop/sku/contactus-request.html.twig', [
                'entities' => $contactUsRequests,
        ]);
    }
}
