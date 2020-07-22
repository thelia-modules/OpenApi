<?php

namespace OpenApi\Model\Api;

use OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestStack;

class ModelFactory
{
    /** @var Container  */
    protected $container;

    protected $locale;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->locale = $container->get('request_stack')->getCurrentRequest()->getSession()->getLang()->getLocale();
    }

    public function buildModel($modelName, $data)
    {
        $openApiModels = $this->container->getParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY);

        // If no correspondent OpenApi model was found
        if (!is_array($openApiModels) || !array_key_exists($modelName, $openApiModels)) {
            return null;
        }

        $modelServiceId = $openApiModels[$modelName];

        /** @var BaseApiModel $model */
        $model = $this->container->get($modelServiceId);
        $model->createOrUpdateFromData($data, $this->locale);
        //$model->createFromData($data);

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