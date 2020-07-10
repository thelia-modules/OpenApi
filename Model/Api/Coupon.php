<?php


namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use Thelia\Model\CouponQuery;

/**
 * Class Coupon
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A coupon"
 * )
 */
class Coupon extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer",
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $code;

    /**
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $amount;

    /**
     * Create an OpenApi Coupon from a Thelia Coupon
     *
     * @param \Thelia\Model\Coupon $coupon
     * @return $this
     */
    public function createFromTheliaCoupon(\Thelia\Model\Coupon $coupon)
    {
        $this->id = $coupon->getId();
        $this->code = $coupon->getCode();
        $this->amount = $coupon->getAmount();

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
     * @return Coupon
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return Coupon
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return Coupon
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }


}