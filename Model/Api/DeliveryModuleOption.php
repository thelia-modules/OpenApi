<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

/**
 * Class DeliveryModuleOption.
 *
 * @OA\Schema(
 *     description="An option for delivery module"
 * )
 */
class DeliveryModuleOption extends BaseApiModel
{
    /**
     * @var string
     * @OA\Property(
     *    type="string",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $code;

    /**
     * @var bool
     * @OA\Property(
     *    type="boolean",
     * )
     */
    protected $valid;

    /**
     * @var string
     * @OA\Property(
     *    type="string",
     * )
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *    type="string",
     * )
     */
    protected $description;

    /**
     * @var string
     * @OA\Property(
     *    type="string",
     *    description="Delivery logo url",
     * )
     */
    protected $image;

    /**
     * @var string
     * @OA\Property(
     *    type="string",
     *    format="date-time",
     * )
     */
    protected $minimumDeliveryDate;

    /**
     * @var string
     * @OA\Property(
     *    type="string",
     *    format="date-time",
     * )
     */
    protected $maximumDeliveryDate;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $postage;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $postageTax;

    /**
     * @var float
     * @OA\Property(
     *    type="number",
     *    format="float",
     * )
     */
    protected $postageUntaxed;

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
     * @return DeliveryModuleOption
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * @return DeliveryModuleOption
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
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
     * @return DeliveryModuleOption
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return DeliveryModuleOption
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     *
     * @return DeliveryModuleOption
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getMinimumDeliveryDate()
    {
        return $this->minimumDeliveryDate;
    }

    /**
     * @param string $minimumDeliveryDate
     *
     * @return DeliveryModuleOption
     */
    public function setMinimumDeliveryDate($minimumDeliveryDate)
    {
        $this->minimumDeliveryDate = $minimumDeliveryDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaximumDeliveryDate()
    {
        return $this->maximumDeliveryDate;
    }

    /**
     * @param string $maximumDeliveryDate
     *
     * @return DeliveryModuleOption
     */
    public function setMaximumDeliveryDate($maximumDeliveryDate)
    {
        $this->maximumDeliveryDate = $maximumDeliveryDate;

        return $this;
    }

    /**
     * @return float
     */
    public function getPostage()
    {
        return $this->postage;
    }

    /**
     * @param float $postage
     *
     * @return DeliveryModuleOption
     */
    public function setPostage($postage)
    {
        $this->postage = $postage;

        return $this;
    }

    /**
     * @return float
     */
    public function getPostageTax()
    {
        return $this->postageTax;
    }

    /**
     * @param float $postageTax
     *
     * @return DeliveryModuleOption
     */
    public function setPostageTax($postageTax)
    {
        $this->postageTax = $postageTax;

        return $this;
    }

    /**
     * @return float
     */
    public function getPostageUntaxed()
    {
        return $this->postageUntaxed;
    }

    /**
     * @param float $postageUntaxed
     *
     * @return DeliveryModuleOption
     */
    public function setPostageUntaxed($postageUntaxed)
    {
        $this->postageUntaxed = $postageUntaxed;

        return $this;
    }
}
