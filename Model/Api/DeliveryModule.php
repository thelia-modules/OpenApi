<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Exception\OpenApiException;
use OpenApi\Model\Api\ModelTrait\translatable;
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
    use translatable;

    /**
     * @var string
    * @OA\Property(ref="#/components/schemas/DeliveryMode")
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
