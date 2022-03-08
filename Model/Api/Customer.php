<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint;

/**
 * @OA\Schema(
 *     schema="Customer",
 *     title="Customer",
 *     description="Customer model"
 * )
 */
class Customer extends BaseApiModel
{
    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var CivilityTitle
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/CivilityTitle",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $civilityTitle;

    /**
     * @var Lang
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Lang",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $lang;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"update"})
     */
    protected $reference;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $firstName;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $lastName;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $email;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $rememberMe;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $discount;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $reseller;

    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $defaultAddressId;

    public function createFromTheliaModel($theliaModel, $locale = null): void
    {
        parent::createFromTheliaModel($theliaModel, $locale);
        $this->setDefaultAddressId($theliaModel->getDefaultAddress()->getId());
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
     * @return Customer
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Customer
     */
    public function setCivilityTitle($civilityTitle)
    {
        $this->civilityTitle = $civilityTitle;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     *
     * @return Customer
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return Customer
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

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
     * @return Customer
     */
    public function setFirstname($firstName)
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
     * @return Customer
     */
    public function setLastname($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRememberMe()
    {
        return $this->rememberMe;
    }

    /**
     * @param bool $rememberMe
     *
     * @return Customer
     */
    public function setRememberMe($rememberMe)
    {
        $this->rememberMe = $rememberMe;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     *
     * @return Customer
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReseller()
    {
        return $this->reseller;
    }

    /**
     * @param bool $reseller
     *
     * @return Customer
     */
    public function setReseller($reseller)
    {
        $this->reseller = $reseller;

        return $this;
    }

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
    public function getLangId()
    {
        return $this->getLang()->getId();
    }

    public function setTitle(CivilityTitle $civilityTitle)
    {
        $this->civilityTitle = $civilityTitle;

        return $this;
    }

    public function setRef($ref)
    {
        $this->reference = $ref;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultAddressId()
    {
        return $this->defaultAddressId;
    }

    /**
     * @param int $id
     *
     * @return Customer
     */
    public function setDefaultAddressId($id)
    {
        $this->defaultAddressId = $id;

        return $this;
    }
}
