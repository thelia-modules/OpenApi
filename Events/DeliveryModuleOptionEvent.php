<?php


namespace OpenApi\Events;


use OpenApi\Model\Api\DeliveryModuleOption;
use phpDocumentor\Reflection\Types\Boolean;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Address;
use Thelia\Model\Cart;
use Thelia\Model\Country;
use Thelia\Model\Module;
use Thelia\Model\OrderPostage;
use Thelia\Model\State;
use Thelia\Module\AbstractDeliveryModule;

class DeliveryModuleOptionEvent extends ActionEvent
{
    /** @var Module */
    protected $module;

    /** @var Cart */
    protected $cart;

    /** @var Address */
    protected $address;

    /** @var Country */
    protected $country;

    /** @var State */
    protected $state;

    /** ------ */

    /** @var array  */
    protected $deliveryModuleOptions = [];

    public function __construct(
        Module $module,
        Address $address,
        Cart $cart = null,
        Country $country = null,
        State $state = null
    ) {
        $this->module = $module;
        $this->address = $address;
        $this->cart = $cart;
        $this->country = $country;
        $this->state = $state;

        if (null === $this->module || null === $this->address) {
            throw new \Exception(Translator::getInstance()->trans('Not enough informations to retrieve module options'));
        }

        if (!$module->isDeliveryModule()) {
            throw new \Exception(Translator::getInstance()->trans($module->getTitle() . ' is not a delivery module.'));
        }
    }

    /**
     * @return array
     */
    public function getDeliveryModuleOptions()
    {
        return $this->deliveryModuleOptions;
    }

    /**
     * @param array $deliveryModuleOptions
     * @return DeliveryModuleOptionEvent
     */
    public function setDeliveryModuleOptions($deliveryModuleOptions)
    {
        $this->deliveryModuleOptions = $deliveryModuleOptions;
        return $this;
    }

    /**
     * @param DeliveryModuleOption $deliveryModuleOption
     * @return DeliveryModuleOptionEvent
     */
    public function appendDeliveryModuleOptions($deliveryModuleOption)
    {
        $this->deliveryModuleOptions[] = $deliveryModuleOption;
        return $this;
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param Module $module
     * @return DeliveryModuleOptionEvent
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Cart $cart
     * @return DeliveryModuleOptionEvent
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return DeliveryModuleOptionEvent
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return DeliveryModuleOptionEvent
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     * @return DeliveryModuleOptionEvent
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }


}