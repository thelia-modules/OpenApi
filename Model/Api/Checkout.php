<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Order;

/**
 * Class Checkout.
 *
 * @OA\Schema(
 *     schema="Checkout",
 *     title="Checkout",
 *     description="Checkout model",
 * )
 */
class Checkout extends BaseApiModel
{
    protected $isComplete = false;

    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     *    description="id of the delivery module used by this checkout"
     * )
     */
    protected $deliveryModuleId;

    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     *    description="id of the payment module used by this checkout"
     * )
     */
    protected $paymentModuleId;

    /**
     * @var int
     * @OA\Property(
     *    type="integer"
     * )
     */
    protected $billingAddressId;

    /**
     * @var int
     * @OA\Property(
     *    type="integer"
     * )
     */
    protected $deliveryAddressId;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $deliveryModuleOptionCode;

    /**
     * @var Address
     * @OA\Property(
     *     ref="#/components/schemas/Address"
     * )
     */
    protected $pickupAddress;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *    @OA\Items(
     *           ref="#/components/schemas/PaymentModuleOptionChoice"
     *    )
     * )
     */
    protected $paymentOptionChoices = [];

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean"
     * )
     */
    protected $acceptedTermsAndConditions = false;

    public function createFromOrder(Order $order)
    {
        $this->setDeliveryAddressId($order->getChoosenDeliveryAddress());
        $this->setBillingAddressId($order->getChoosenInvoiceAddress());

        $this->setDeliveryModuleId($order->getDeliveryModuleId())
            ->setPaymentModuleId($order->getPaymentModuleId());

        return $this;
    }

    /**
     *  @OA\Property(
     *     property="isComplete",
     *     description="Tell if a checkout has defined a Module and an Address for both delivery and billing",
     *     type="boolean"
     *  )
     *
     * @return bool
     */
    public function getIsComplete()
    {
        if (null === $this->getDeliveryModuleId()) {
            return false;
        }

        if (null === $this->getDeliveryAddressId()) {
            return false;
        }

        if (null === $this->getBillingAddressId()) {
            return false;
        }

        if (null === $this->getPaymentModuleId()) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function checkIsValid(): void
    {
        if (null === $this->getDeliveryModuleId()) {
            throw new \Exception(
                Translator::getInstance()->trans(
                    'You must choose a delivery module',
                    [],
                    OpenApi::DOMAIN_NAME
                )
            );
        }

        if (null === $this->getPaymentModuleId()) {
            throw new \Exception(
                Translator::getInstance()->trans(
                    'You must choose a payment module',
                    [],
                    OpenApi::DOMAIN_NAME
                )
            );
        }

        if (null === $this->getDeliveryAddressId()) {
            throw new \Exception(
                Translator::getInstance()->trans(
                    'You must choose a delivery address',
                    [],
                    OpenApi::DOMAIN_NAME
                )
            );
        }

        if (null === $this->getBillingAddressId()) {
            throw new \Exception(
                Translator::getInstance()->trans(
                    'You must choose a billing address',
                    [],
                    OpenApi::DOMAIN_NAME
                )
            );
        }

        if (false === $this->isAcceptedTermsAndConditions()) {
            throw new \Exception(
                Translator::getInstance()->trans(
                    'You must accept the terms and conditions',
                    [],
                    OpenApi::DOMAIN_NAME
                )
            );
        }
    }

    /**
     * @return int
     */
    public function getDeliveryModuleId()
    {
        return $this->deliveryModuleId;
    }

    /**
     * @param int $deliveryModuleId
     *
     * @return Checkout
     */
    public function setDeliveryModuleId($deliveryModuleId)
    {
        $this->deliveryModuleId = $deliveryModuleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentModuleId()
    {
        return $this->paymentModuleId;
    }

    /**
     * @param int $paymentModuleId
     *
     * @return Checkout
     */
    public function setPaymentModuleId($paymentModuleId)
    {
        $this->paymentModuleId = $paymentModuleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getBillingAddressId()
    {
        return $this->billingAddressId;
    }

    /**
     * @param int $billingAddressId
     *
     * @return Checkout
     */
    public function setBillingAddressId($billingAddressId)
    {
        $this->billingAddressId = $billingAddressId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    /**
     * @param int $deliveryAddressId
     *
     * @return Checkout
     */
    public function setDeliveryAddressId($deliveryAddressId)
    {
        $this->deliveryAddressId = $deliveryAddressId;

        return $this;
    }

    /**
     * @return Address
     */
    public function getPickupAddress()
    {
        return $this->pickupAddress;
    }

    /**
     * @param Address $pickupAddress
     *
     * @return Checkout
     */
    public function setPickupAddress($pickupAddress)
    {
        $this->pickupAddress = $pickupAddress;

        return $this;
    }

    public function isAcceptedTermsAndConditions(): bool
    {
        return $this->acceptedTermsAndConditions;
    }

    public function setAcceptedTermsAndConditions(bool $acceptedTermsAndConditions): self
    {
        $this->acceptedTermsAndConditions = $acceptedTermsAndConditions;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryModuleOptionCode()
    {
        return $this->deliveryModuleOptionCode;
    }

    /**
     * @param $deliveryModuleOptionCode
     * @return Checkout
     */
    public function setDeliveryModuleOptionCode($deliveryModuleOptionCode)
    {
        $this->deliveryModuleOptionCode = $deliveryModuleOptionCode;
        return $this;
    }

    public function getPaymentOptionChoices(): array
    {
        return $this->paymentOptionChoices;
    }

    public function setPaymentOptionChoices(?array $paymentOptionChoices = []): Checkout
    {
        $this->paymentOptionChoices = $paymentOptionChoices;
        return $this;
    }
}
