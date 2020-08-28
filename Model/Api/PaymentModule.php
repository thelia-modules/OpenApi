<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class PaymentModule
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A module of type payment"
 * )
 */
class PaymentModule extends BaseApiModel
{
    use translatable;
    /**
     * @var integer
     * @OA\Property(
     *    type="integer"
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var boolean
     * @OA\Property(
     *    type="boolean"
     * )
     */
    protected $valid;

    /**
     * @var integer
     * @OA\Property(
     *    type="integer"
     * )
     */
    protected $code;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float"
     * )
     */
    protected $minimumAmount;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float"
     * )
     */
    protected $maximumAmount;

    /**
     * @var string
     * @OA\Property(
     *    type="string",
     *    description="Payment logo url"
     * )
     */
    protected $image;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PaymentModule
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     * @return PaymentModule
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return PaymentModule
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return float
     */
    public function getMinimumAmount()
    {
        return $this->minimumAmount;
    }

    /**
     * @param float $minimumAmount
     * @return PaymentModule
     */
    public function setMinimumAmount($minimumAmount)
    {
        $this->minimumAmount = $minimumAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaximumAmount()
    {
        return $this->maximumAmount;
    }

    /**
     * @param float $maximumAmount
     * @return PaymentModule
     */
    public function setMaximumAmount($maximumAmount)
    {
        $this->maximumAmount = $maximumAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return PaymentModule
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
}