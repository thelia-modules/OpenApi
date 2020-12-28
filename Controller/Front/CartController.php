<?php

namespace OpenApi\Controller\Front;

use OpenApi\OpenApi;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Cart;
use Thelia\Model\CartItemQuery;
use Thelia\Model\Country;
use Thelia\Model\CouponQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Model\State;
use Thelia\Module\BaseModule;
use Thelia\Module\Exception\DeliveryException;

/**
 * @Route("/cart", name="cart")
 */
class CartController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="get_cart", methods="GET")
     *
     * @OA\Get(
     *     path="/cart",
     *     tags={"cart"},
     *     summary="Get cart currently in session",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getCart(Request $request)
    {
        return $this->createResponseFromCart($request->getSession()->getSessionCart($this->getDispatcher()));
    }

    /**
     * @Route("/add", name="add_cartitem", methods="POST")
     *
     * @OA\Post(
     *     path="/cart/add",
     *     tags={"cart"},
     *     summary="Add a PSE in a cart",
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="pseId",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="quantity",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="append",
     *                     type="boolean"
     *                 ),
     *                 example={"pseId": 18, "quantity": 2, "append": true}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="cart",
     *                     ref="#/components/schemas/Cart"
     *                 ),
     *                 @OA\Property(
     *                     property="cartItem",
     *                     ref="#/components/schemas/CartItem"
     *                 )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function cartAddCartItem(Request $request)
    {
        $cart = $request->getSession()->getSessionCart($this->getDispatcher());
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        $event = new CartEvent($cart);

        $this->updateCartEventFromJson($request->getContent(), $event);
        $this->getDispatcher()->dispatch(TheliaEvents::CART_ADDITEM, $event);

        return $this->jsonResponse([
            'cart' => $this->getCurrentOpenApiCart($cart),
            'cartItem' => $this->getModelFactory()->buildModel('CartItem', $event->getCartItem())
        ]);
    }

    /**
     * @Route("/{cartItemId}", name="delete_cartitem", methods="DELETE")
     *
     * @OA\Delete(
     *     path="/cart/{cartItemId}",
     *     tags={"cart"},
     *     summary="Delete an item in the current cart",
     *     @OA\Parameter(
     *          name="cartItemId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="cart",
     *                     ref="#/components/schemas/Cart"
     *                 ),
     *                 @OA\Property(
     *                     property="cartItem",
     *                     ref="#/components/schemas/CartItem"
     *                 )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function cartDeleteCartItem(Request $request, $cartItemId)
    {
        $cart = $request->getSession()->getSessionCart($this->getDispatcher());
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();

        if (null === $cartItem) {
            throw new \Exception(Translator::getInstance()->trans("Deletion impossible : this cart item does not exists.", [], OpenApi::DOMAIN_NAME));
        }

        $cartEvent = new CartEvent($cart);
        $cartEvent->setCartItemId($cartItemId);

        $this->getDispatcher()->dispatch(
            TheliaEvents::CART_DELETEITEM,
            $cartEvent
        );

        return $this->createResponseFromCart($cart);
    }

    /**
     * @Route("/{cartItemId}", name="update_cartitem", methods="PATCH")
     *
     * @OA\Patch(
     *     path="/cart/{cartItemId}",
     *     tags={"cart"},
     *     summary="Modify an item in the current cart",
     *     @OA\Parameter(
     *          name="cartItemId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="quantity",
     *                     type="integer"
     *                 ),
     *                 example={"quantity": 0}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function cartUpdateCartItem(Request $request, $cartItemId)
    {
        $cart = $request->getSession()->getSessionCart($this->getDispatcher());
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();

        if (null === $cartItem) {
            throw new \Exception(Translator::getInstance()->trans("Modification impossible : this cart item does not exists.", [], OpenApi::DOMAIN_NAME));
        }

        /** Check if cart item belongs to user's cart */
        if (!$cartItem || $cartItem->getCartId() !== $cart->getId()) {
            throw new \Exception(Translator::getInstance()->trans("This cartItem doesn't belong to this cart.", [], OpenApi::DOMAIN_NAME));
        }

        $event = new CartEvent($cart);
        $event->setCartItemId($cartItemId);
        $this->updateCartEventFromJson($request->getContent(), $event);
        $this->dispatch(TheliaEvents::CART_UPDATEITEM, $event);

        return $this->jsonResponse([
            'cart' => $this->getCurrentOpenApiCart($cart),
            'cartItem' => $this->getModelFactory()->buildModel('CartItem', $event->getCartItem())
        ]);
    }

    /**
     * Create a new JSON response of an OpenApi cart and returns it, from a Thelia Cart
     *
     * @param Cart $cart
     * @return JsonResponse
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createResponseFromCart(Cart $cart)
    {
        return $this->jsonResponse($this->getCurrentOpenApiCart($cart));
    }

    protected function getCurrentOpenApiCart($cart)
    {
        $currentDeliveryCountry = $this->container->get('thelia.taxEngine')->getDeliveryCountry();
        $currentDeliveryState = $this->container->get('thelia.taxEngine')->getDeliveryState();
        $estimatedPostage = $this->getEstimatedPostageForCountry($cart, $currentDeliveryCountry, $currentDeliveryState);
        $coupons = $this->createOpenApiCouponsFromCouponsCodes($this->getSession()->getConsumedCoupons());

        /** @var \OpenApi\Model\Api\Cart $openApiCart */
        $openApiCart = $this->getModelFactory()->buildModel('Cart');
        $openApiCart->fillFromSessionCart(
            $cart,
            $currentDeliveryCountry,
            $coupons,
            $estimatedPostage
        );

        return $openApiCart;
    }

    /**
     * Update a Cart Event from a json
     *
     * @param $json
     * @param CartEvent $event
     * @throws \Exception
     */
    protected function updateCartEventFromJson($json, CartEvent $event)
    {
        $data = json_decode($json, true);

        if (!isset($data['quantity'])) {
            throw new \Exception(Translator::getInstance()->trans('A quantity is needed in the POST request to add an item to the cart.'));
        }

        /** If the function was called from the PATCH route, we just update the quantity and return */
        if ($cartItemId = $event->getCartItemId()) {
            $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();
            if ($data['quantity'] >= $cartItem->getProductSaleElements()->getQuantity()) {
                throw new \Exception(Translator::getInstance()->trans('Desired quantity exceed available stock'));
            }
            $event->setQuantity($data['quantity']);
            return ;
        }

        /** If the function was called from the POST route, we need to set the pseId and append properties, as we need a new CartItem */
        if (!isset($data['pseId'])) {
            throw new \Exception(Translator::getInstance()->trans('A PSE is needed in the POST request to add an item to the cart.'));
        }
        if (!isset($data['append'])) {
            throw new \Exception(Translator::getInstance()->trans('You need to set the append value in the POST request to add an item to the cart.'));
        }

        $availableQuantity = ProductSaleElementsQuery::create()
            ->filterById($data['pseId'])
            ->findOne()
            ->getQuantity()
        ;

        if ($data['quantity'] > $availableQuantity) {
            throw new \Exception(Translator::getInstance()->trans('Desired quantity exceed available stock'));
        }

        $event
            ->setProduct(ProductSaleElementsQuery::create()->findPk($data['pseId'])->getProductId())
            ->setProductSaleElementsId($data['pseId'])
            ->setQuantity($data['quantity'])
            ->setAppend($data['append'])
        ;
    }

    /**
     * Creates an array of OpenApi coupons from an array of coupons codes, then returns it
     *
     * @param $couponsCodes
     * @return array
     */
    protected function createOpenApiCouponsFromCouponsCodes($couponsCodes)
    {
        $coupons = CouponQuery::create()->filterByCode($couponsCodes)->find();

        $factory = $this->getModelFactory();
        return array_map(
            function ($coupon) use ($factory) {
                return $factory->buildModel('Coupon', $coupon);
            },
            iterator_to_array($coupons)
        );
    }

    /**
     * Return the minimum expected postage for a cart in a given country
     *
     * @param Cart $cart
     * @param Country $country
     * @param State|null $state
     * @return float|null
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getEstimatedPostageForCountry(Cart $cart, Country $country, State $state = null)
    {
        $deliveryModules = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE, Criteria::EQUAL)
            ->find()
        ;

        $virtual = $cart->isVirtual();
        $postage = null;

        /** @var \Thelia\Model\Module $deliveryModule */
        foreach ($deliveryModules as $deliveryModule) {
            $areaDeliveryModule = AreaDeliveryModuleQuery::create()
                ->findByCountryAndModule($country, $deliveryModule, $state);

            if (null === $areaDeliveryModule && false === $virtual) {
                continue;
            }

            $moduleInstance = $deliveryModule->getDeliveryModuleInstance($this->container);

            if (true === $virtual && false === $moduleInstance->handleVirtualProductDelivery()) {
                continue;
            }

            try {
                $deliveryPostageEvent = new DeliveryPostageEvent($moduleInstance, $cart, null, $country, $state);
                $this->getDispatcher()->dispatch(
                    TheliaEvents::MODULE_DELIVERY_GET_POSTAGE,
                    $deliveryPostageEvent
                );

                if ($deliveryPostageEvent->isValidModule()) {
                    $modulePostage = $deliveryPostageEvent->getPostage();

                    if (null === $postage || $postage > $modulePostage->getAmount()) {
                        $postage = $modulePostage->getAmount();
                    }
                }
            } catch (DeliveryException $ex) {
                // Module is not available
            }
        }

        return $postage;
    }
}
