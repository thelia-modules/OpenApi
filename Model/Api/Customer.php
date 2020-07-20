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
     * @var integer
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read", "update"})
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
     * @var Language
     * @OA\Property(
     *    type="object",
     *    ref="#/components/schemas/Language",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $lang;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $reference;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $firstname;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $lastname;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $email;

    /**
     * @var boolean
     * @OA\Property(
     *    type="boolean",
     * )
     * @Constraint\NotNull(groups={"create", "update"})
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
     * @var boolean
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
     * @return Customer
     */
    public function setCivilityTitle($civilityTitle)
    {
        $this->civilityTitle = $civilityTitle;
        return $this;
    }

    /**
     * @return Language
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param Language $lang
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
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Customer
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Customer
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
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
     * @return Customer
     */
    public function setReseller($reseller)
    {
        $this->reseller = $reseller;
        return $this;
    }

    /** Thelia model creation functions */

    /**
     * @return \Thelia\Model\Customer
     */
    protected function getTheliaModel()
    {
        return new \Thelia\Model\Customer();
    }

    /**
     * @return integer
     */
    public function getTitleId()
    {
        return $this->getCivilityTitle()->getId();
    }

    public function setLangId()
    {
        return $this->getLang()->getId();
    }
}