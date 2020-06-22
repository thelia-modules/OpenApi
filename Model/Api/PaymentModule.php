<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class PaymentModule
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A module of type payment"
 * )
 */
class PaymentModule extends BaseApiModel
{
    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $id;

    /**
     *  @OA\Property(
     *     type="boolean"
     *  )
     */
    protected $valid;

    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $code;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $title;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $description;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $chapo;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $postscriptum;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float"
     *  )
     */
    protected $minimumAmount;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float"
     *  )
     */
    protected $maximumAmount;

    /**
     *  @OA\Property(
     *     type="string",
     *     description="Payment logo url"
     *  )
     */
    protected $image;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return PaymentModule
     */
    public function setId($id)
    {
        $this->id = $id;
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
     *
     * @return PaymentModule
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
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
     *
     * @return PaymentModule
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     *
     * @return PaymentModule
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return PaymentModule
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @param mixed $chapo
     *
     * @return PaymentModule
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    /**
     * @param mixed $postscriptum
     *
     * @return PaymentModule
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumAmount()
    {
        return $this->minimumAmount;
    }

    /**
     * @param mixed $minimumAmount
     *
     * @return PaymentModule
     */
    public function setMinimumAmount($minimumAmount)
    {
        $this->minimumAmount = $minimumAmount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaximumAmount()
    {
        return $this->maximumAmount;
    }

    /**
     * @param mixed $maximumAmount
     *
     * @return PaymentModule
     */
    public function setMaximumAmount($maximumAmount)
    {
        $this->maximumAmount = $maximumAmount;
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
     * @return PaymentModule
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
}