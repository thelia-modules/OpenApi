<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use Thelia\Model\CountryQuery;
use Thelia\Model\Address as TheliaAddress;

/**
 * @OA\Schema(
 *     schema="Address",
 *     title="Address",
 *     description="Address model"
 * )
 *
 */
class Address extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer"
     * )
     * @Constraint\NotBlank(groups={"read", "update"})
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="boolean"
     * )
     * @Constraint\NotNull(groups={"create", "update"})
     */
    protected $isDefault;

    /**
     * @OA\Property(
     *     type="string",
     *     description="The name for this address",
     * )
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
     * @OA\Property(
     *     type="string",
     * ),
     * @Constraint\NotNull(groups={"create","update"})
     */
    protected $firstName;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotNull(groups={"create","update"})
     */
    protected $lastName;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $cellphoneNumber;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $phoneNumber;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $company;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create","update"})
     */
    protected $address1;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address2;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address3;

    /**
     * @OA\Property(
     *     type="string",
     * ),
     * @Constraint\NotBlank(groups={"create","update"})
     */
    protected $zipCode;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create","update"})
     */
    protected $city;

    /**
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
     * @OA\Property(
     *     type="object"
     * )
     */
    protected $additionalData;

    /**
     * @param TheliaAddress $address
     * @param string $locale
     * @return $this|Address
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaModel($address, $locale = 'en_US')
    {
        parent::createFromTheliaModel($address, $locale);

        $customerTitle = $address->getCustomerTitle()
            ->setLocale($locale);

        $civ = $this->modelFactory->buildModel('Title', $customerTitle);

        $this
            ->setCivilityTitle($civ)
            ->setCountryCode($address->getCountry()->getIsoalpha2())
        ;

        return $this;
    }

    public function toTheliaModel($locale = null)
    {
        /** @var TheliaAddress $address */
        $address = parent::toTheliaModel($locale);
        $address->setNew(false);

        return $address;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Address
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param mixed $isDefault
     *
     * @return Address
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     *
     * @return Address
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     *
     * @return Address
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     *
     * @return Address
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCellphoneNumber()
    {
        return $this->cellphoneNumber;
    }

    /**
     * @param mixed $cellphoneNumber
     *
     * @return Address
     */
    public function setCellphoneNumber($cellphoneNumber)
    {
        $this->cellphoneNumber = $cellphoneNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     *
     * @return Address
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     *
     * @return Address
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param mixed $address1
     *
     * @return Address
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param mixed $address2
     *
     * @return Address
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * @param mixed $address3
     *
     * @return Address
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     *
     * @return Address
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     *
     * @return Address
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @param mixed $additionalData
     *
     * @return Address
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
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
     * @param Customer|BaseApiModel $customer
     * @return Address
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /** Thelia model creation functions */

//    protected function getTheliaModel()
//    {
//        return parent::getTheliaModel();
//        $this->getId() ? $address = AddressQuery::create()->findPk($this->getId()) : $address = new TheliaAddress();
//
//        return $address;
//    }

    /**
     * @return int
     */
    public function getTitleId()
    {
        return $this->getCivilityTitle()->getId();
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }

    public function getCountryId()
    {
        return CountryQuery::create()->filterByIsoalpha2($this->getCountryCode())->findOne()->getId();
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

}