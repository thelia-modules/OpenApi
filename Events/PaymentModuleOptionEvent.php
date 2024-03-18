<?php

namespace OpenApi\Events;

use OpenApi\Model\Api\PaymentModuleOption;
use OpenApi\Model\Api\PaymentModuleOptionGroup;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Address;
use Thelia\Model\Base\CountryQuery;
use Thelia\Model\Cart;
use Thelia\Model\Country;
use Thelia\Model\Module;
use Thelia\Model\State;

class PaymentModuleOptionEvent extends ActionEvent
{
    protected Module $module;

    protected Cart $cart;

    protected ?Address $address = null;

    protected ?Country $country = null;

    protected ?State $state = null;

    protected array $paymentModuleOptionGroups = [];

    public function __construct(
        Module $module,
        Cart $cart = null,
    ) {
        $this->module = $module;
        $address = $cart?->getAddressRelatedByAddressInvoiceId() ?? $cart?->getAddressRelatedByAddressDeliveryId() ?? null;
        $this->address = $address;
        $this->cart = $cart;
        $this->country = $address?->getCountry() ?? CountryQuery::create()->filterByByDefault(true)->findOne();
        $this->state = $address?->getState() ?? $this->country?->getStates()?->getFirst() ?? null;

        if (!$module->isPayementModule()) {
            throw new \Exception(Translator::getInstance()->trans($module->getTitle().' is not a payment module.'));
        }
    }

    public function getPaymentModuleOptionGroups()
    {
        return $this->paymentModuleOptionGroups;
    }

    public function setPaymentModuleOptionGroups($paymentModuleOptionGroups)
    {
        $this->paymentModuleOptionGroups = $paymentModuleOptionGroups;

        return $this;
    }

    public function appendPaymentModuleOptionGroups(PaymentModuleOptionGroup $paymentModuleOptionGroup)
    {
        $this->paymentModuleOptionGroups[] = $paymentModuleOptionGroup;

        return $this;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}
