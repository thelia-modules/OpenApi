<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

/**
 * Class Coupon.
 *
 * @OA\Schema(
 *     description="A coupon"
 * )
 */
class Coupon extends BaseApiModel
{
    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read", "update"})
     */
    protected $id;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $code;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $shortDescription;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $amount;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     * @Constraint\NotBlank(groups={"create", "update"})
     */
    protected $percentage;

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
     * @return Coupon
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Coupon
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Coupon
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param float $amount
     *
     * @return Coupon
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param float $percentage
     *
     * @return Coupon
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     * @return Coupon
     */
    public function setShortDescription(string $shortDescription): Coupon
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
