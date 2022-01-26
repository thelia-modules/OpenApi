<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use Thelia\Model\Address as TheliaAddress;
use Thelia\Model\CountryQuery;
use Thelia\Model\StateQuery;

/**
 * @OA\Schema(
 *     schema="Address",
 *     title="Address",
 *     description="Address model"
 * )
 */
class Address extends BaseApiModel
{
    public static $serviceAliases = ["PickupAddress"];

    /**
     * @var int
     * @OA\Property(
     *    type="integer"
     * )
     * @Constraint\NotBlank(groups={"read", "update"})
     */
    protected $id;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean"
     * )
     * @Constraint\NotNull(groups={"create", "update"})
     */
    protected $isDefault;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     *     description="The name for this address",
     * )
     * @Constraint\NotBlank(groups={"create","update"})
     */
    protected $label;

    /**
     * @var Customer
     * @OA\Property(
     *     ref="#/components/schemas/Customer"
     * ),
     * @Constraint\NotNull(groups={"create","update"})
     */
    protected $customer;

    /**
     * @var CivilityTitle
     * @OA\Property(
     *     ref="#/components/schemas/CivilityTitle"
     * ),
     * @Constraint\NotNull(groups={"create","update"})
     */
    protected $civilityTitle;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * ),
     * @Constraint\NotNull(groups={"create","update"})
     */
    protected $firstName;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotNull(groups={"create","update"})
     */
    protected $lastName;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $cellphoneNumber;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $phoneNumber;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $company;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create","update"})
     */
    protected $address1;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address2;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address3;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * ),
     * @Constraint\NotBlank(groups={"create","update"})
     * @Constraint\Zipcode(groups={"create","update"})
     */
    protected $zipCode;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create","update"})
     */
    protected $city;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     *     description="Country ISO 3166-1 alpha-2 code"
     * )
     * @Constraint\NotBlank(groups={"create","update"})
     * @Constraint\Length(
     *      min = 2,
     *      max = 2
     * )
     */
    protected $countryCode;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $stateCode;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $stateName;

    /**
     * @var object
     * @OA\Property(
     *     type="object"
     * )
     */
    protected $additionalData;

    /**
     * @param TheliaAddress $address
     * @param string        $locale
     *
     * @return $this|Address
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaModel($address, $locale = 'en_US')
    {
        parent::createFromTheliaModel($address, $locale);

        $customerTitle = $address->getCustomerTitle()
            ->setLocale($locale);

        /** @var CivilityTitle $civ */
        $civ = $this->modelFactory->buildModel('Title', $customerTitle);

        $this
            ->setCivilityTitle($civ)
            ->setCountryCode($address->getCountry()->getIsoalpha2())
        ;
        if (null !== $state = $address->getState()) {
            $this
                ->setStateCode($state->getIsocode())
                ->setStateName($state->setLocale($locale)->getTitle());
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Address
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     *
     * @return Address
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Address
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return Address
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return CivilityTitle
     */
    public function getCivilityTitle()
    {
        return $this->civilityTitle;
    }

    /**
     * @param CivilityTitle $civilityTitle
     *
     * @return Address
     */
    public function setCivilityTitle($civilityTitle)
    {
        $this->civilityTitle = $civilityTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return Address
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return Address
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCellphoneNumber()
    {
        return $this->cellphoneNumber;
    }

    /**
     * @param string $cellphoneNumber
     *
     * @return Address
     */
    public function setCellphoneNumber($cellphoneNumber)
    {
        $this->cellphoneNumber = $cellphoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     *
     * @return Address
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     *
     * @return Address
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     *
     * @return Address
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     *
     * @return Address
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * @param string $address3
     *
     * @return Address
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     *
     * @return Address
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return Address
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return object
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @param object $additionalData
     *
     * @return Address
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    /** Thelia model creation functions */

    /**
     * @return int
     */
    public function getTitleId()
    {
        $civilityTitle = $this->getCivilityTitle();

        return null !== $civilityTitle ? $civilityTitle->getId() : null;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $customer = $this->getCustomer();

        return null !== $customer ? $customer->getId() : null;
    }

    public function getCountryId()
    {
        $country = CountryQuery::create()->filterByIsoalpha2($this->getCountryCode())->findOne();

        return null !== $country ? $country->getId() : null;
    }

    public function getPhone()
    {
        return $this->phoneNumber;
    }

    public function getCellPhone()
    {
        return $this->cellphoneNumber;
    }

    public function setPhone($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function setCellphone($cellphoneNumber)
    {
        $this->cellphoneNumber = $cellphoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStateCode(): ?string
    {
        return $this->stateCode;
    }

    /**
     * @param string|null $stateCode
     */
    public function setStateCode(string $stateCode = null)
    {
        $this->stateCode = $stateCode;

        return $this;
    }

    public function getStateId()
    {
        $state = StateQuery::create()->filterByCountryId($this->getCountryId())->filterByIsocode($this->getStateCode())->findOne();

        return null !== $state ? $state->getId() : null;
    }

    /**
     * @return string
     */
    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    /**
     * @param string $stateName
     */
    public function setStateName(string $stateName = null)
    {
        $this->stateName = $stateName;

        return $this;
    }
}
