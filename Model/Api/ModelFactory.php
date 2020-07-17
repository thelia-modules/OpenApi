<?php

namespace OpenApi\Model\Api;

use OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Container;

class ModelFactory
{
    /** @var Container  */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildModel($modelName, $data)
    {
        if (!defined(OpenApi::OPEN_API_MODELS_CONSTANT_KEY)) {
            return null;
        }

        $openApiModels = constant(OpenApi::OPEN_API_MODELS_CONSTANT_KEY);
        if (!in_array($modelName, $openApiModels)) {
            return null;
        }

        $modelServiceId = $openApiModels[$modelName];

        /** @var BaseApiModel $model */
        $model = $this->container->get($modelServiceId);
        $model->createFromData($data);

        return $model;
    }
}