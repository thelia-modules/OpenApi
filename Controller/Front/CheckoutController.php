<?php

namespace OpenApi\Controller\Front;

use Front\Front;
use OpenApi\Annotations as OA;
use OpenApi\Model\Api\Checkout;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AddressQuery;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\Cart;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Module\Exception\DeliveryException;

/**
 * @Route("/checkout")
 */
class CheckoutController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="set_checkout", methods="POST")
     * @OA\Post(
     *     path="/checkout",
     *     tags={"checkout"},
     *     summary="Validate and set an checkout",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          allOf={
     *               @OA\Schema(@OA\Property(property="needValidate", type="boolean", default=false)),
     *               @OA\Schema(ref="#/components/schemas/Checkout")
     *          }
     *     )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Checkout")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function setCheckout(
        Request $request,
        Session $session,
        EventDispatcherInterface $dispatcher,
        SecurityContext $securityContext,
        OpenApiService $openApiService,
        ModelFactory $modelFactory
    ) {
        // Allow to check if a customer is logged
        $openApiService->getCurrentCustomer();

        $cart = $session->getSessionCart($dispatcher);
        if ($cart === null || $cart->countCartItems() === 0) {
            throw new \Exception(Translator::getInstance()->trans('Cart is empty', [], OpenApi::DOMAIN_NAME));
        }

        if (true === ConfigQuery::checkAvailableStock()) {
            if (!$this->checkStockNotEmpty($cart)) {
                throw new \Exception(Translator::getInstance()->trans('Not enough stock', [], OpenApi::DOMAIN_NAME));
            }
        }

        $decodedContent = json_decode($request->getContent(), true);

        /** @var Checkout $checkout */
        $checkout = $modelFactory->buildModel('Checkout', $decodedContent);

        if (isset($decodedContent['needValidate']) && true === $decodedContent['needValidate']) {
            $checkout->checkIsValid();
        }

        $order = $this->getOrder($request);
        $orderEvent = new OrderEvent($order);

        $this->setOrderDeliveryPart(
            $request,
            $session,
            $dispatcher,
            $securityContext,
            $checkout,
            $orderEvent
        );
        $this->setOrderInvoicePart(
            $dispatcher,
            $securityContext,
            $checkout,
            $orderEvent
        );

        $responseCheckout = $checkout
            ->createFromOrder($orderEvent->getOrder());

        return OpenApiService::jsonResponse($responseCheckout);
    }

    /**
     * @Route("", name="get_checkout", methods="GET")
     * @OA\Get(
     *     path="/checkout",
     *     tags={"checkout"},
     *     summary="get current checkout",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Checkout"),
     *     )
     * )
     */
    public function getCheckout(
        Request $request,
        ModelFactory $modelFactory
    ) {
        $order = $this->getOrder($request);

        /** @var Checkout $checkout */
        $checkout = ($modelFactory->buildModel('Checkout'))
            ->createFromOrder($order);

        $checkout->setPickupAddress($request->getSession()->get(OpenApi::PICKUP_ADDRESS_SESSION_KEY));

        return OpenApiService::jsonResponse($checkout);
    }

    protected function setOrderDeliveryPart(
        Request $request,
        Session $session,
        EventDispatcherInterface $dispatcher,
        SecurityContext $securityContext,
        Checkout $checkout,
        OrderEvent $orderEvent
    ): void {
        $cart = $session->getSessionCart($dispatcher);
        $deliveryAddress = AddressQuery::create()->findPk($checkout->getDeliveryAddressId());
        $deliveryModule = ModuleQuery::create()->findPk($checkout->getDeliveryModuleId());

        /** In case of pickup point delivery, we cannot use a Thelia address since it won't exist, so we get one from the request */
        $pickupAddress = $checkout->getPickupAddress();

        if (null !== $deliveryAddress) {
            if ($deliveryAddress->getCustomerId() !== $securityContext->getCustomerUser()->getId()) {
                throw new \Exception(
                    Translator::getInstance()->trans(
                        'Delivery address does not belong to the current customer',
                        [],
                        Front::MESSAGE_DOMAIN
                    )
                );
            }
        }

        if (null !== $pickupAddress && $deliveryAddress && $deliveryModule) {
            if (null === AreaDeliveryModuleQuery::create()->findByCountryAndModule(
                    $deliveryAddress->getCountry(),
                    $deliveryModule
                )) {
                throw new \Exception(
                    Translator::getInstance()->trans(
                        'Delivery module cannot be use with selected delivery address',
                        [],
                        Front::MESSAGE_DOMAIN
                    )
                );
            }
        }

        $postage = null;
        if ($deliveryAddress && $deliveryModule) {
            $moduleInstance = $deliveryModule->getDeliveryModuleInstance($this->container);

            $deliveryPostageEvent = new DeliveryPostageEvent($moduleInstance, $cart, $deliveryAddress);

            $dispatcher->dispatch(
                $deliveryPostageEvent,
                TheliaEvents::MODULE_DELIVERY_GET_POSTAGE
            );

            if (!$deliveryPostageEvent->isValidModule()) {
                throw new DeliveryException(
                    Translator::getInstance()->trans('The delivery module is not valid.', [], Front::MESSAGE_DOMAIN)
                );
            }

            $postage = $deliveryPostageEvent->getPostage();
        }
        
        $orderEvent->setDeliveryAddress($deliveryAddress !== null ? $deliveryAddress->getId() : $securityContext->getCustomerUser()?->getDefaultAddress()?->getId());
        $orderEvent->setDeliveryModule($deliveryModule?->getId());
        $orderEvent->setPostage($postage !== null ? $postage->getAmount() : 0.0);
        $orderEvent->setPostageTax($postage !== null ? $postage->getAmountTax() : 0.0);
        $orderEvent->setPostageTaxRuleTitle($postage !== null ? $postage->getTaxRuleTitle() : '');

        $dispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_DELIVERY_ADDRESS);
        $dispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_POSTAGE);
        $dispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_DELIVERY_MODULE);

        if ($deliveryAddress && $deliveryModule) {
            $this->checkValidDelivery();
        }

        $request->getSession()->set(OpenApi::PICKUP_ADDRESS_SESSION_KEY, json_encode($pickupAddress));
    }

    protected function setOrderInvoicePart(
        EventDispatcherInterface $dispatcher,
        SecurityContext $securityContext,
        Checkout $checkout,
        OrderEvent $orderEvent
    ): void {
        $billingAddress = AddressQuery::create()->findPk($checkout->getBillingAddressId());

        if ($billingAddress) {
            if ($billingAddress->getCustomerId() !== $securityContext->getCustomerUser()->getId()) {
                throw new \Exception(
                    Translator::getInstance()->trans(
                        'Invoice address does not belong to the current customer',
                        [],
                        Front::MESSAGE_DOMAIN
                    )
                );
            }
        }

        $paymentModule = ModuleQuery::create()->findPk($checkout->getPaymentModuleId());

        $orderEvent->setInvoiceAddress($billingAddress !== null ? $billingAddress->getId() : null);
        $orderEvent->setPaymentModule($paymentModule !== null ? $paymentModule->getId() : null);
        $dispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_INVOICE_ADDRESS);
        $dispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_PAYMENT_MODULE);

        // Only check invoice is module and address is set
        if ($billingAddress && $paymentModule) {
            $this->checkValidInvoice();
        }
    }

    protected function getOrder(Request $request)
    {
        $session = $request->getSession();

        if (null !== $order = $session->getOrder()) {
            return $order;
        }

        $order = new Order();

        $session->setOrder($order);

        return $order;
    }

    protected function checkValidDelivery(): void
    {
        $order = $this->getSession()->getOrder();
        if (null === $order
            ||
            null === $order->getChoosenDeliveryAddress()
            ||
            null === $order->getDeliveryModuleId()
            ||
            null === AddressQuery::create()->findPk($order->getChoosenDeliveryAddress())
            ||
            null === ModuleQuery::create()->findPk($order->getDeliveryModuleId())) {
            throw new \Exception(Translator::getInstance()->trans('Invalid delivery', [], OpenApi::DOMAIN_NAME));
        }
    }

    protected function checkValidInvoice(): void
    {
        $order = $this->getSession()->getOrder();
        if (null === $order
            ||
            null === $order->getChoosenInvoiceAddress()
            ||
            null === $order->getPaymentModuleId()
            ||
            null === AddressQuery::create()->findPk($order->getChoosenInvoiceAddress())
            ||
            null === ModuleQuery::create()->findPk($order->getPaymentModuleId())) {
            throw new \Exception(Translator::getInstance()->trans('Invalid invoice', [], OpenApi::DOMAIN_NAME));
        }
    }

    protected function checkStockNotEmpty(Cart $cart)
    {
        $cartItems = $cart->getCartItems();

        foreach ($cartItems as $cartItem) {
            $pse = $cartItem->getProductSaleElements();

            $product = $cartItem->getProduct();

            if ($pse->getQuantity() <= 0 && $product->getVirtual() !== 1) {
                return false;
            }
        }

        return true;
    }
}
