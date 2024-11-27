<?php

namespace OpenApi\Controller\Front;

use Exception;
use OpenApi\Annotations as OA;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Base\ProductPriceQuery;
use Thelia\Model\Cart;
use Thelia\Model\CartItemQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ProductSaleElements;
use Thelia\Model\ProductSaleElementsQuery;

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
    public function getCart(OpenApiService $openApiService)
    {
        return $this->createResponseFromCart($openApiService);
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
     *                 @OA\Property(
     *                     property="newness",
     *                     type="boolean"
     *                 ),
     *                 example={"pseId": 18, "quantity": 2, "append": true, "newness": true}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="cart",
     *                 ref="#/components/schemas/Cart"
     *             ),
     *             @OA\Property(
     *                 property="cartItem",
     *                 ref="#/components/schemas/CartItem"
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function cartAddCartItem(
        Request $request,
        EventDispatcherInterface $dispatcher,
        OpenApiService $openApiService,
        ModelFactory $modelFactory
    ) {
        $cart = $request->getSession()->getSessionCart($dispatcher);
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        $event = new CartEvent($cart);
        $data = json_decode($request->getContent(), true);

        $this->updateCartEventFromJson($data, $event);
        $dispatcher->dispatch($event, TheliaEvents::CART_ADDITEM);

        return OpenApiService::jsonResponse([
            'cart' => $openApiService->getCurrentOpenApiCart(),
            'cartItem' => $modelFactory->buildModel('CartItem', $event->getCartItem()),
        ]);
    }

    /**
     * @Route("/add_multiple", name="add_cartitem_mutliple", methods="POST")
     *
     * @OA\Post(
     *     path="/cart/add_multiple",
     *     tags={"cart"},
     *     summary="Add multiple PSE in a cart",
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *              type="array",
     *              @OA\Items(
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
     *                 @OA\Property(
     *                     property="newness",
     *                     type="boolean"
     *                 ),
     *                 example={"pseId": 18, "quantity": 2, "append": true, "newness": true}
     *              )
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                property="cart",
     *                ref="#/components/schemas/Cart"
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
     public function cartAddMultiple(
         Request $request,
         EventDispatcherInterface $dispatcher,
         OpenApiService $openApiService
     ) {
         $cart = $request->getSession()->getSessionCart($dispatcher);
         if (null === $cart) {
             throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
         }

         $data = json_decode($request->getContent(), true);

         $errors = [];

         foreach ($data as $index => $item) {
             try {
                 $event = new CartEvent($cart);
                 $this->updateCartEventFromJson($item, $event);
                 $dispatcher->dispatch($event, TheliaEvents::CART_ADDITEM);
             } catch (\Throwable $th) {
                 if (isset($item['pseId'])) {
                     $errors['pses'][$item['pseId']] = $th->getMessage();
                 } else {
                     $errors['unknown'][$index] = $th->getMessage();
                 }
             }
         }

         if (count($errors) > 0) {
             throw new Exception(json_encode($errors));
         }

         return OpenApiService::jsonResponse([
             'cart' => $openApiService->getCurrentOpenApiCart()
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
      *              type="array",
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
    public function cartDeleteCartItem(
        Request $request,
        EventDispatcherInterface $dispatcher,
        OpenApiService $openApiService,
        $cartItemId
    ) {
        $cart = $request->getSession()->getSessionCart($dispatcher);
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();

        if (null === $cartItem) {
            throw new \Exception(Translator::getInstance()->trans('Deletion impossible : this cart item does not exists.', [], OpenApi::DOMAIN_NAME));
        }

        $cartEvent = new CartEvent($cart);
        $cartEvent->setCartItemId($cartItemId);

        $dispatcher->dispatch(
            $cartEvent,
            TheliaEvents::CART_DELETEITEM
        );

        return $this->createResponseFromCart($openApiService);
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
    public function cartUpdateCartItem(
        Request $request,
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory,
        OpenApiService $openApiService,
        $cartItemId
    ) {
        $cart = $request->getSession()->getSessionCart($dispatcher);
        if (null === $cart) {
            throw new \Exception(Translator::getInstance()->trans('No cart found', [], OpenApi::DOMAIN_NAME));
        }

        $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();

        if (null === $cartItem) {
            throw new \Exception(Translator::getInstance()->trans('Modification impossible : this cart item does not exists.', [], OpenApi::DOMAIN_NAME));
        }

        /* Check if cart item belongs to user's cart */
        if (!$cartItem || $cartItem->getCartId() !== $cart->getId()) {
            throw new \Exception(Translator::getInstance()->trans("This cartItem doesn't belong to this cart.", [], OpenApi::DOMAIN_NAME));
        }

        $event = new CartEvent($cart);
        $event->setCartItemId($cartItemId);

        if ($request->get('quantity') === 0) {
            $dispatcher->dispatch(
                $event,
                TheliaEvents::CART_DELETEITEM
            );
        } else {
            $data = json_decode($request->getContent(), true);
            $this->updateCartEventFromJson($data, $event);
            $dispatcher->dispatch($event, TheliaEvents::CART_UPDATEITEM);
        }

        return OpenApiService::jsonResponse([
            'cart' => $openApiService->getCurrentOpenApiCart(),
            'cartItem' => $modelFactory->buildModel('CartItem', $event->getCartItem()),
        ]);
    }

    /**
     * Create a new JSON response of an OpenApi cart and returns it, from a Thelia Cart.
     *
     * @param Cart $cart
     *
     * @return JsonResponse
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createResponseFromCart(OpenApiService $openApiService)
    {
        return OpenApiService::jsonResponse($openApiService->getCurrentOpenApiCart());
    }

    /**
     * @param ProductSaleElements $pse
     * @param int $quantity
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function checkAvailableStock(ProductSaleElements $pse, int $quantity): bool
    {
        return $quantity > $pse->getQuantity() &&
            ConfigQuery::checkAvailableStock() && !$pse->getProduct()->getVirtual();
    }

    /**
     * Update a Cart Event from a json.
     *
     * @param $json
     *
     * @throws \Exception
     */
    protected function updateCartEventFromJson($data, CartEvent $event): void
    {
        if (!isset($data['quantity'])) {
            throw new \Exception(Translator::getInstance()->trans('A quantity is needed in the POST request to add an item to the cart.'));
        }

        /* If the function was called from the PATCH route, we just update the quantity,pse,product id and return */
        if ($cartItemId = $event->getCartItemId()) {
            $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();
            if ($this->checkAvailableStock($cartItem->getProductSaleElements(), $data['quantity'])) {
                throw new \Exception(Translator::getInstance()->trans('Desired quantity exceed available stock'));
            }
            $event->setQuantity($data['quantity']);
            if (!isset($data['pseId'])) {
                return;
            }
            $cartItem = CartItemQuery::create()->filterById($cartItemId)->findOne();
            $pse = ProductSaleElementsQuery::create()->findPk($data['pseId']);
            $productPrice = ProductPriceQuery::create()->findOneByProductSaleElementsId($data['pseId']);
            $cartItem
                ->setProductId($pse->getProductId())
                ->setProductSaleElementsId($data['pseId'])
                ->setPrice($productPrice->getPrice())
                ->setPromoPrice($productPrice->getPromoPrice())
                ->save();
            return;
        }

        /* If the function was called from the POST route, we need to set the pseId and append properties, as we need a new CartItem */
        if (!isset($data['pseId'])) {
            throw new \Exception(Translator::getInstance()->trans('A PSE is needed in the POST request to add an item to the cart.'));
        }
        if (!isset($data['append'])) {
            throw new \Exception(Translator::getInstance()->trans('You need to set the append value in the POST request to add an item to the cart.'));
        }

        $pse = ProductSaleElementsQuery::create()->findPk($data['pseId']);

        if ($this->checkAvailableStock($pse, $data['quantity'])) {
            throw new \Exception(Translator::getInstance()->trans('Desired quantity exceed available stock'));
        }

        /** If newness then force new cart_item id */
        $newness = isset($data['newness']) ? (bool) $data['newness'] : false;

        $event
            ->setProduct($pse->getProductId())
            ->setProductSaleElementsId($data['pseId'])
            ->setQuantity($data['quantity'])
            ->setAppend($data['append'])
            ->setNewness($newness)
        ;
    }
}
