<?php

namespace OpenApi\Model\Api;

use OpenApi\OpenApi;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Thelia\Log\Tlog;

class ModelFactory
{
    /** @var Container */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildModel($modelName, $data = null, $locale = null)
    {
        try {
            $openApiModels = $this->container->getParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY);

            // If no correspondent OpenApi model was found
            if (!\is_array($openApiModels) || !\array_key_exists($modelName, $openApiModels)) {
                $modelName = rtrim($modelName, 's');
                // Try to remove trailing "s" for plural
                if (!\array_key_exists($modelName, $openApiModels)) {
                    return null;
                }
            }

            $modelServiceId = $openApiModels[$modelName];

            /** @var BaseApiModel $model */
            $model = $this->container->get($modelServiceId);

            if (null !== $data) {
                $model->createOrUpdateFromData($data, $locale);
            }

            return $model;
        } catch (\Exception $exception) {
            Tlog::getInstance()->addError("Error for building api model \"$modelName\" : ".$exception->getMessage());

            return null;
        }
    }

    public function modelExists($modelName)
    {
        $openApiModels = $this->container->getParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY);

        if (!\is_array($openApiModels) || !\array_key_exists($modelName, $openApiModels)) {
            return false;
        }

        return true;
    }
}
