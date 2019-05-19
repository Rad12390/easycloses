<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\ShopBundle\Entity\Sku;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * Add SKU to User Cart
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function storeAction(Request $request)
    {
        $paymentId = $request->get('payment_id', null);
        $skuId = $request->get('sku_id', null);
        $optionsRaw = $request->get('options', []);
        $options = [];

        if (!empty($optionsRaw)) {
            foreach ($optionsRaw as $option) {
                $val = explode('_', $option);
                $options[$val[0]] = $val[1];
            }
        }

        $em = $this->getDoctrine()->getManager();
        $quantity = (int)$request->request->get('quantity', 1);

        $cart = $this->getCart($request);

        /** @var Sku $sku */
        $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($skuId);
        $needToChoose = false;

        if ($options == [] && count($sku->getOptions()) > 0) {
            $needToChoose = true;
        }

        if ($paymentId === null) {
            if (count($sku->getPrices()) > 1) {
                $needToChoose = true;
            } elseif (count($sku->getPrices())) {
                $paymentId = $sku->getPrices()->first()->getId();
            }
        }

        if ($needToChoose === true) {

            $html = $this->renderView('@LocalsBestShop/cart/choose-block.html.twig', [
                'prices' => $sku->getPrices(),
                'options' => $sku->getOptions(),
                'entity' => $sku,
            ]);

            return new JsonResponse([
                'error' => 0,
                'data' => [
                    'needToChoose' => true,
                    'html' => $html
                ],
            ]);
        }

        $cart[$skuId] = [
            'paymentId' => $paymentId,
            'quantity' => $quantity,
            'options' => $options
        ];

        $response = new Response();
        $cookie = new Cookie(
            $this->getParameter('cookie_cart'),
            json_encode($cart),
            time() + (3600*365),
            '/',
            null,
            false,
            false
        );
        $response->headers->setCookie($cookie);
        $response->send();

        $skus = [];
        foreach ($cart as $item) {
            $array = [];
            $array['quantity'] = $item['quantity'];
            $price = $em->getRepository('LocalsBestShopBundle:Price')->find($item['paymentId']);
            $array['price'] = $price->getAmount();
            $array['paymentId'] = $item['paymentId'];
            $array['options'] = $item['options'];
            $array['sku'] = $price->getSku();

            $skus[] = $array;
        }

        return new JsonResponse([
            'error' => 0,
            'message' => 'Product added successfully to Your Cart.',
            'header_box' => $this->renderView('@LocalsBestShop/cart/header-box-content.html.twig', ['entities' => $skus]),
            'data' => [
            ],
        ]);
    }

    /**
     * Displays User Cart
     *
     * @param Request $request.
     * @return array
     *
     * @Template()
     */
    public function showAction(Request $request)
    {
        $skus = $this->getSkus($request);
        $limited_sku =[];
        foreach($skus as $sku)
        if($sku['sku']->getPackage()->getType()!=2){
            array_push($limited_sku, $sku);
        }

        return [
            'skus' => $skus,
            'limited_sku' => $limited_sku
        ];
    }

    /**
     * Get SKUs that in Cart
     *
     * @param $request
     *
     * @return array
     */
    private function getSkus($request)
    {
        $data = $this->getCart($request);

        $em = $this->getDoctrine()->getManager();
        $skus = [];

        foreach ($data as $item) {
            if ($item['paymentId'] === null) {
                continue;
            }
            $array = [];
            $array['quantity'] = $item['quantity'];
            $price = $em->getRepository('LocalsBestShopBundle:Price')->find($item['paymentId']);
            $array['price'] = $price->getAmount();
            $array['options'] = $item['options'];
            $array['sku'] = $price->getSku();

            $skus[] = $array;
        }

        return $skus;
    }

    /**
     * Remove Product from User Cart
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     *
     */
    public function removeAction(Request $request, $id)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);
        $skuId = (int)$id;

        if ($cart === null) {
            $cart = [];
        } else {
            $cart = json_decode($cart, true);
        }

        if (isset($cart[$skuId])) {
            unset($cart[$skuId]);
        }

        $response = new Response();
        $cookie = new Cookie($cookieName, json_encode($cart), time() + (3600*365), '/', null, false, false);
        $response->headers->setCookie($cookie);
        $response->send();

        return $this->redirectToRoute('cart_show');
    }

    /**
     * Update quantity for SKU in Cart
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateQuantityAction(Request $request)
    {
        $cart = $this->getCart($request);

        $skuId = $request->request->get('sku');
        $quantity = $request->request->get('quantity');

        $result = [
            'result' => 'error',
            'massage' => 'Nothing to update.',
        ];

        if (isset($cart[$skuId])) {
            $cart[$skuId]['quantity'] = $quantity;

            $response = new Response();
            $cookie = new Cookie(
                $this->getParameter('cookie_cart'),
                json_encode($cart),
                time() + (3600*365),
                '/',
                null,
                false,
                false
            );
            $response->headers->setCookie($cookie);
            $response->send();

            $result = [
                'result' => 'success',
                'massage' => 'Quantity successfully updated',
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * Render Cart block in Header
     *
     * @param Request $request
     *
     * @return Response
     */
    public function headerBoxAction(Request $request)
    {
        $entities = $this->getSkus($request);

        return $this->render('@LocalsBestShop/cart/header-box.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * Get Cart from Cookies
     *
     * @param Request $request
     *
     * @return array|mixed|null
     */
    private function getCart($request)
    {
        $cookieName = $this->getParameter('cookie_cart');
        $cart = $request->cookies->get($cookieName);

        if ($cart === null) {
            $cart = [];
        } else {
            $cart = json_decode($cart, true);
        }

        return $cart;
    }
}
