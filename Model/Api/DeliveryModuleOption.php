<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class DeliveryModuleOption
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An option for delivery module"
 * )
 */
class DeliveryModuleOption extends BaseApiModel
{
    /**
     *  @OA\Property(
     *     type="string",
     *  )
     */
    protected $code;

    /**
     *  @OA\Property(
     *     type="boolean",
     *  )
     */
    protected $valid;

    /**
     *  @OA\Property(
     *     type="string",
     *  )
     */
    protected $title;

    /**
     *  @OA\Property(
     *     type="string",
     *     description="Delivery logo url",
     *  )
     */
    protected $image;

    /**
     *  @OA\Property(
     *     type="string",
     *     format="date-time",
     *  )
     */
    protected $minimumDeliveryDate;

    /**
     *  @OA\Property(
     *     type="string",
     *     format="date-time",
     *  )
     */
    protected $maximumDeliveryDate;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float",
     *  )
     */
    protected $postage;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float",
     *  )
     */
    protected $postageTax;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float",
     *  )
     */
    protected $postageUntaxed;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return DeliveryModuleOption
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @param mixed $valid
     * @return DeliveryModuleOption
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return DeliveryModuleOption
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     *
     * @return DeliveryModuleOption
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumDeliveryDate()
    {
        return $this->minimumDeliveryDate;
    }

    /**
     * @param mixed $minimumDeliveryDate
     *
     * @return DeliveryModuleOption
     */
    public function setMinimumDeliveryDate($minimumDeliveryDate)
    {
        $this->minimumDeliveryDate = $minimumDeliveryDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaximumDeliveryDate()
    {
        return $this->maximumDeliveryDate;
    }

    /**
     * @param mixed $maximumDeliveryDate
     *
     * @return DeliveryModuleOption
     */
    public function setMaximumDeliveryDate($maximumDeliveryDate)
    {
        $this->maximumDeliveryDate = $maximumDeliveryDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostage()
    {
        return $this->postage;
    }

    /**
     * @param mixed $postage
     *
     * @return DeliveryModuleOption
     */
    public function setPostage($postage)
    {
        $this->postage = $postage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostageTax()
    {
        return $this->postageTax;
    }

    /**
     * @param mixed $postageTax
     *
     * @return DeliveryModuleOption
     */
    public function setPostageTax($postageTax)
    {
        $this->postageTax = $postageTax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostageUntaxed()
    {
        return $this->postageUntaxed;
    }

    /**
     * @param mixed $postageUntaxed
     *
     * @return DeliveryModuleOption
     */
    public function setPostageUntaxed($postageUntaxed)
    {
        $this->postageUntaxed = $postageUntaxed;
        return $this;
    }
}