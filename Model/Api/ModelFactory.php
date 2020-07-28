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

    public function buildModel($modelName, $data = null)
    {
        $openApiModels = $this->container->getParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY);

        // If no correspondent OpenApi model was found
        if (!is_array($openApiModels) || !array_key_exists($modelName, $openApiModels)) {
            return null;
        }

        $modelServiceId = $openApiModels[$modelName];

        /** @var BaseApiModel $model */
        $model = $this->container->get($modelServiceId);

        if (null !== $data) {
            $model->createOrUpdateFromData($data);
        }

        return $model;
    }

    public function modelExists($modelName)
    {
        $openApiModels = $this->container->getParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY);

        if (!is_array($openApiModels) || !array_key_exists($modelName, $openApiModels)) {
            return false;
        }

        return true;
    }
}