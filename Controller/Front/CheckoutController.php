<?php

namespace OpenApi\Controller\Front;

use Front\Front;
use OpenApi\Model\Api\Address;
use OpenApi\Model\Api\Checkout;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AddressQuery;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Model\ConfigQuery;
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
     *          @OA\JsonContent(ref="#/components/schemas/Checkout")
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
    public function setCheckout(Request $request)
    {
        $this->getCurrentCustomer();

        $cart = $this->getSession()->getSessionCart($this->getDispatcher());
        if ($cart === null || $cart->countCartItems() === 0) {
            throw new \Exception(Translator::getInstance()->trans('Cart is empty', [], OpenApi::DOMAIN_NAME));
        }

        if (true === ConfigQuery::checkAvailableStock()) {
            if (!$this->checkStockNotEmpty()) {
                throw new \Exception(Translator::getInstance()->trans('Not enough stock', [], OpenApi::DOMAIN_NAME));
            }
        }

        /** @var Checkout $checkout */
        $checkout = $this->getModelFactory()->buildModel('Checkout', $request->getContent());
        $checkout->checkIsValid();

        $order = $this->getOrder($this->getRequest());
        $orderEvent = new OrderEvent($order);

        $this->setOrderDeliveryPart($checkout, $orderEvent);
        $this->setOrderInvoicePart($checkout, $orderEvent);

        $responseCheckout = $checkout
            ->createFromOrder($orderEvent->getOrder());


        return $this->jsonResponse($responseCheckout);
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
    public function getCheckout(Request $request)
    {
        $order = $this->getOrder($request);

        /** @var Checkout $checkout */
        $checkout = ($this->getModelFactory()->buildModel('Checkout'))
            ->createFromOrder($order);

        $checkout->setPickupAddress($request->getSession()->get(OpenApi::PICKUP_ADDRESS_SESSION_KEY));

        return $this->jsonResponse($checkout);
    }

    protected function setOrderDeliveryPart(Checkout $checkout, OrderEvent $orderEvent)
    {
        $cart = $this->getSession()->getSessionCart($this->getDispatcher());
        $deliveryAddress = AddressQuery::create()->findPk($checkout->getDeliveryAddressId());
        $deliveryModule = ModuleQuery::create()->findPk($checkout->getDeliveryModuleId());

        /** In case of pickup point delivery, we cannot use a Thelia address since it won't exist, so we get one from the request */
        $pickupAddress = $checkout->getPickupAddress();

        if (null !== $deliveryAddress) {
            if ($deliveryAddress->getCustomerId() !== $this->getSecurityContext()->getCustomerUser()->getId()) {
                throw new \Exception(
                    $this->getTranslator()->trans(
                        "Delivery address does not belong to the current customer",
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
                    $this->getTranslator()->trans(
                        "Delivery module cannot be use with selected delivery address",
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

            $this->getDispatcher()->dispatch(
                TheliaEvents::MODULE_DELIVERY_GET_POSTAGE,
                $deliveryPostageEvent
            );

            if (!$deliveryPostageEvent->isValidModule()) {
                throw new DeliveryException(
                    $this->getTranslator()->trans('The delivery module is not valid.', [], Front::MESSAGE_DOMAIN)
                );
            }

            $postage = $deliveryPostageEvent->getPostage();
        }

        $orderEvent->setDeliveryAddress($deliveryAddress !== null ? $deliveryAddress->getId() : null);
        $orderEvent->setDeliveryModule($deliveryModule !== null ? $deliveryModule->getId() : null);
        $orderEvent->setPostage($postage !== null ? $postage->getAmount() : 0.0);
        $orderEvent->setPostageTax($postage !== null ? $postage->getAmountTax() : 0.0);
        $orderEvent->setPostageTaxRuleTitle($postage !== null ? $postage->getTaxRuleTitle() : "");

        $this->getDispatcher()->dispatch(TheliaEvents::ORDER_SET_DELIVERY_ADDRESS, $orderEvent);
        $this->getDispatcher()->dispatch(TheliaEvents::ORDER_SET_POSTAGE, $orderEvent);
        $this->getDispatcher()->dispatch(TheliaEvents::ORDER_SET_DELIVERY_MODULE, $orderEvent);

        if ($deliveryAddress && $deliveryModule) {
            $this->checkValidDelivery();
        }

        $this->getRequest()->getSession()->set(OpenApi::PICKUP_ADDRESS_SESSION_KEY, json_encode($pickupAddress));
    }

    protected function setOrderInvoicePart(Checkout $checkout, OrderEvent $orderEvent)
    {
        $billingAddress = AddressQuery::create()->findPk($checkout->getBillingAddressId());

        if ($billingAddress) {
            if ($billingAddress->getCustomerId() !== $this->getSecurityContext()->getCustomerUser()->getId()) {
                throw new \Exception(
                    $this->getTranslator()->trans(
                        "Invoice address does not belong to the current customer",
                        [],
                        Front::MESSAGE_DOMAIN
                    )
                );
            }
        }

        $paymentModule = ModuleQuery::create()->findPk($checkout->getPaymentModuleId());

        $orderEvent->setInvoiceAddress($billingAddress !== null ? $billingAddress->getId() : null);
        $orderEvent->setPaymentModule($paymentModule !== null ? $paymentModule->getId() : null);
        $this->getDispatcher()->dispatch(TheliaEvents::ORDER_SET_INVOICE_ADDRESS, $orderEvent);
        $this->getDispatcher()->dispatch(TheliaEvents::ORDER_SET_PAYMENT_MODULE, $orderEvent);

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

    protected function checkValidDelivery()
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

    protected function checkValidInvoice()
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

    protected function checkStockNotEmpty()
    {
        $cart = $this->getSession()->getSessionCart($this->getDispatcher());

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
