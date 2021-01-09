<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Exception\OpenApiException;
use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;
use OpenApi\Constraint as Constraint;

/**
 * Class DeliveryMode
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A type of delivery"
 * )
 */
class DeliveryMode extends BaseApiModel
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
     * @return string
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * @param string $deliveryMode
     *
     * @return DeliveryMode
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
}
