<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

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
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read", "update"})
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/CivilityTitle",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $civilityTitle;

    /**
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Language",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $lang;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $reference;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $firstname;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $lastname;

    /**
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $email;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     * @Constraint\NotNull(groups={"create", "update"})
     */
    protected $rememberMe;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $discount;

    /**
     * @OA\Property(
     *    type="boolean",
     * )
     * @Constraint\NotNull(groups={"create", "update"})
     */
    protected $reseller;

    /**
     * Creates an OpenApi customer from a Thelia Customer, then returns it
     *
     * @param \Thelia\Model\Customer $customer
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromTheliaCustomer(\Thelia\Model\Customer $customer)
    {
        $this->id = $customer->getId();
        $this->civilityTitle = (new CivilityTitle())->createFromTheliaCustomerTitle($customer->getCustomerTitle());
        $this->lang = (new Language())->createFromTheliaLang($customer->getCustomerLang());
        $this->reference = $customer->getRef();
        $this->firstname = $customer->getFirstname();
        $this->lastname = $customer->getLastname();
        $this->email = $customer->getEmail();
        $this->rememberMe = $customer->getRememberMeToken() ? true : false;
        $this->discount = (float)$customer->getDiscount();
        $this->reseller = $customer->getReseller();

        return $this;
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
     * @return Customer
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCivilityTitle()
    {
        return $this->civilityTitle;
    }

    /**
     * @param mixed $civilityTitle
     * @return Customer
     */
    public function setCivilityTitle($civilityTitle)
    {
        $this->civilityTitle = $civilityTitle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     * @return Customer
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     * @return Customer
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return Customer
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return Customer
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRememberMe()
    {
        return $this->rememberMe;
    }

    /**
     * @param mixed $rememberMe
     * @return Customer
     */
    public function setRememberMe($rememberMe)
    {
        $this->rememberMe = $rememberMe;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     * @return Customer
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReseller()
    {
        return $this->reseller;
    }

    /**
     * @param mixed $reseller
     * @return Customer
     */
    public function setReseller($reseller)
    {
        $this->reseller = $reseller;
        return $this;
    }


}