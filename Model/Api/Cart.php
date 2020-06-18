<?php

namespace OpenApi\Model\Api;

use Thelia\Model\Address;

class Cart extends BaseApiModel
{
    /**
     * @var Address|null
     */
    private $addressModel;
    /**
     * @var string|string|null
     */
    private $address;
    /**
     * @var string|string|null
     */
    private $city;
    /**
     * @var string|string|null
     */
    private $zipCode;
    /**
     * @var State|null
     */
    private $state;
    /**
     * @var Country|null
     */
    private $country;
    /**
     * @var int|integer|null
     */
    private $radius;
    /**
     * @var array|null
     */
    private $moduleIds;

    /**
     * PickupLocationEvent constructor.
     *
     * @param Address|null $addressModel
     * @param string|null $address
     * @param string|null $city
     * @param string|null $zipCode
     * @param State|null $state
     * @param Country|null $country
     * @param integer|null $radius
     * @param array|null $moduleIds
     */
    public function __construct(
        Address $addressModel = null,
        string $address = null,
        string $city = null,
        string $zipCode = null,
        State $state = null,
        Country $country = null,
        integer $radius = null,
        array $moduleIds = null
    ) {

        $this->addressModel = $addressModel;
        $this->address = $address;
        $this->city = $city;
        $this->zipCode = $zipCode;
        $this->state = $state;
        $this->country = $country;
        $this->radius = $radius;
        $this->moduleIds = $moduleIds;
    }
}