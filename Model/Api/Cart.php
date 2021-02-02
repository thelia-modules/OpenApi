<?php

namespace OpenApi\Model\Api;

use Thelia\Model\Country;
use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

/**
 * Class Cart
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A cart"
 * )
 */
class Cart extends BaseApiModel
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
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $taxes;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     *    description="The estimated delivery price for this cart",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $delivery;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Coupon"
     *     )
     * )
     */
    protected $coupons;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $discount;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $total;

    /**
     * @var string
     * @OA\Property(
     *     description="Symbol of the currently used currency",
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $currency;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/CartItem"
     *     )
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $items;

    /**
     * Fill the model from a Thelia Cart in session, a Country, an array
     * of OpenApi coupons, and an estimated postage, then returns it
     *
     * @param \Thelia\Model\Cart $cart
     * @param Country $deliveryCountry
     * @param $coupons
     * @param $estimatedPostage
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function fillFromSessionCart(\Thelia\Model\Cart $cart, Country $deliveryCountry, $coupons, $estimatedPostage)
    {
        $modelFactory = $this->modelFactory;
        $cartItems = array_map(
            function ($theliaCartItem) use ($modelFactory, $deliveryCountry) {
                /** @var CartItem $cartItem */
                $cartItem = $modelFactory->buildModel('CartItem', $theliaCartItem);
                $cartItem->fillFromTheliaCartItemAndCountry($theliaCartItem, $deliveryCountry);
                return $cartItem;
            },
            iterator_to_array($cart->getCartItems())
        );

        $this
            ->setId($cart->getId())
            ->setTaxes($cart->getTotalVAT($deliveryCountry, null, false))
            ->setDelivery($estimatedPostage)
            ->setCoupons($coupons)
            ->setDiscount((float)$cart->getDiscount())
            ->setTotal($cart->getTaxedAmount($deliveryCountry, false, null))
            ->setCurrency($cart->getCurrency()->getSymbol())
            ->setItems($cartItems)
        ;

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
     * @return Cart
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param float $taxes
     * @return Cart
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
        return $this;
    }

    /**
     * @return float
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param float $delivery
     * @return Cart
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return array
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @param array $coupons
     * @return Cart
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;
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
     * @return Cart
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return Cart
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Cart
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Cart
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }
}