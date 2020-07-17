<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;
use OpenApi\Constraint as Constraint;

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
     * @Constraint\NotBlank(groups={"read"})
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
     *     type="string"
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
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/DeliveryModuleOption"
     *     )
     * )
     */
    protected $options;

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
            throw new \Exception(Translator::getInstance()->trans('A delivery module can only be of type pickup or delivery', [], OpenApi::DOMAIN_NAME));
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
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @param mixed $valid
     *
     * @return DeliveryModule
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     * @return DeliveryModule
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }


}