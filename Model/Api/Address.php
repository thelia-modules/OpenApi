<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use Thelia\Model\AddressQuery;
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

    public function createFromData($json)
    {
        parent::createFromData($json);

        $address = json_decode($json, true);
        if (isset($address['civilityTitle'])) {
            $customerTitleId = $address['civilityTitle']['id'];
            $this->setCivilityTitle((new CivilityTitle())->setId($customerTitleId));
        }

        return $this;
    }

    public function createFromTheliaAddress(TheliaAddress $address, $locale = 'en_US')
    {
        $customerTitle = $address->getCustomerTitle()
            ->setLocale($locale);

        $this->setId($address->getId())
            ->setLabel($address->getLabel())
            ->setCivilityTitle((new CivilityTitle())
                ->setId($customerTitle->getId())
                ->setLong($customerTitle->getLong())
                ->setShort($customerTitle->getShort())
            )
            ->setIsDefault($address->getIsDefault())
            ->setFirstName($address->getFirstname())
            ->setLastName($address->getLastname())
            ->setCellphoneNumber($address->getCellphone())
            ->setPhoneNumber($address->getPhone())
            ->setCompany($address->getCompany())
            ->setAddress1($address->getAddress1())
            ->setAddress2($address->getAddress2())
            ->setAddress3($address->getAddress3())
            ->setZipCode($address->getZipcode())
            ->setCity($address->getCity())
            ->setCountryCode($address->getCountry()->getIsoalpha2());

        return $this;
    }

    public function toTheliaAddress()
    {
        $address = $this->id === null ? (new TheliaAddress()) : AddressQuery::create()->findPk($this->id);
        $country = CountryQuery::create()->findOneByIsoalpha2($this->getCountryCode());

        $address->setIsDefault($this->getIsDefault())
            ->setLabel($this->getLabel())
            ->setTitleId($this->getCivilityTitle()->getId())
            ->setFirstname($this->getFirstName())
            ->setLastname($this->getLastName())
            ->setCellphone($this->getCellphoneNumber())
            ->setPhone($this->getPhoneNumber())
            ->setCompany($this->getCompany())
            ->setAddress1($this->getAddress1())
            ->setAddress2($this->getAddress2())
            ->setAddress3($this->getAddress3())
            ->setZipcode($this->getZipCode())
            ->setCity($this->getCity())
            ->setCountry($country);

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
}