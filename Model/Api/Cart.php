<?php

namespace OpenApi\Model\Api;

use OpenApi\Service\ImageService;
use Thelia\Model\Country;
use OpenApi\Annotations as OA;

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
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $taxes;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     *    description="The estimated delivery price for this cart",
     * )
     */
    protected $delivery;

    /**
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/Coupon"
     *     )
     * )
     */
    protected $coupons;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $discount;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $total;

    /**
     * @OA\Property(
     *     description="Symbol of the currently used currency",
     *     type="string",
     * )
     */
    protected $currency;

    /**
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/CartItem"
     *     )
     * )
     */
    protected $items;

    /**
     * Create a new OpenApi Cart from a Thelia Cart in session, a Country, an array
     * of OpenApi coupons, and an estimated postage, then returns it
     *
     * @param \Thelia\Model\Cart $cart
     * @param Country $deliveryCountry
     * @param $coupons
     * @param $estimatedPostage
     * @param ImageService $imageService
     * @return $this
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createFromSessionCart(\Thelia\Model\Cart $cart, Country $deliveryCountry, $coupons, $estimatedPostage, ImageService $imageService)
    {
        $cartItems = [];
        foreach ($cart->getCartItems() as $theliaCartItem) {
            $cartItems[] = (new CartItem())->createFromTheliaCartItemAndCountry($theliaCartItem, $deliveryCountry, $imageService);
        }

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Cart
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param mixed $taxes
     * @return Cart
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param mixed $delivery
     * @return Cart
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @param mixed $coupons
     * @return Cart
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;
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
     * @return Cart
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     * @return Cart
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return Cart
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     * @return Cart
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }


}