<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;

/**
 * Class DeliveryModule
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A module of type delivery"
 * )
 */
class DeliveryModule extends BaseApiModel
{
    /**
     *  @OA\Property(
     *     type="string",
     *     enum={"pickup", "delivery"}
     *  )
     */
    protected $deliveryMode;

    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $id;

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
     *     type="string",
     *     description="Delivery logo url"
     *  )
     */
    protected $image;

    /**
     *  @OA\Property(
     *     type="string",
     *     format="date-time"
     *  )
     */
    protected $minimumDeliveryDate;

    /**
     *  @OA\Property(
     *     type="string",
     *     format="date-time"
     *  )
     */
    protected $maximumDeliveryDate;

    /**
     *  @OA\Property(
     *     type="float"
     *  )
     */
    protected $postage;

    /**
     *  @OA\Property(
     *     type="float"
     *  )
     */
    protected $postageTax;

    /**
     *  @OA\Property(
     *     type="float"
     *  )
     */
    protected $postageUntaxed;

    /**
     * @return mixed
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * @param mixed $deliveryMode
     *
     * @return DeliveryModule
     * @throws \Exception
     */
    public function setDeliveryMode($deliveryMode)
    {
        if (!in_array($deliveryMode, ['pickup', 'delivery'])) {
            throw new \Exception(Translator::getInstance()->trans('A delivery module can only de of type pickup or delivery', [], OpenApi::DOMAIN_NAME));
        }

        $this->deliveryMode = $deliveryMode;
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
     *
     * @return DeliveryModule
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
     *
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;
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
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
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
     * @return DeliveryModule
     */
    public function setPostageUntaxed($postageUntaxed)
    {
        $this->postageUntaxed = $postageUntaxed;
        return $this;
    }
}