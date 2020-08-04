<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Exception\OpenApiException;
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
     * @var string
     * @OA\Property(
     *    type="string",
     *    enum={"pickup", "delivery"}
     * )
     */
    protected $deliveryMode;

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
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $code;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $description;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $chapo;

    /**
     * @var string
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $postscriptum;

    /**
     * @var array
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          ref="#/components/schemas/DeliveryModuleOption"
     *     )
     * )
     */
    protected $options;

    /**
     * @return string
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * @param string $deliveryMode
     *
     * @return DeliveryModule
     * @throws \Exception
     */
    public function setDeliveryMode($deliveryMode)
    {
        if (!in_array($deliveryMode, ['pickup', 'delivery'])) {
            /** @var Error $error */
            $error = $this->modelFactory->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans("Delivery mode can only be 'pickup' or 'delivery'", [], OpenApi::DOMAIN_NAME),
                ]
            );

            throw new OpenApiException($error);
        }

        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return DeliveryModule
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
     * @return DeliveryModule
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
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
     * @return DeliveryModule
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return DeliveryModule
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
     * @return DeliveryModule
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @param string $chapo
     * @return DeliveryModule
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    /**
     * @param string $postscriptum
     * @return DeliveryModule
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return DeliveryModule
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }


}