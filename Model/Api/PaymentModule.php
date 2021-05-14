<?php

namespace OpenApi\Model\Api;

use OpenApi\Constraint as Constraint;
use OpenApi\Model\Api\ModelTrait\translatable;

/**
 * Class PaymentModule.
 *
 * @OA\Schema(
 *     description="A module of type payment"
 * )
 */
class PaymentModule extends BaseApiModel
{
    use translatable;

    /**
     * @var int
     * @OA\Property(
     *    type="integer"
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean"
     * )
     */
    protected $valid;

    /**
     * @var int
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
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/File"
     *     )
     * )
     */
    protected $images = [];

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
     *
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
     *
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
     *
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
     *
     * @return PaymentModule
     */
    public function setMaximumAmount($maximumAmount)
    {
        $this->maximumAmount = $maximumAmount;

        return $this;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return PaymentModule
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }
}
