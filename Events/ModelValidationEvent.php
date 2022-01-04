<?php

namespace OpenApi\Events;

use OpenApi\Model\Api\BaseApiModel;
use OpenApi\Model\Api\ModelFactory;
use Thelia\Core\Event\ActionEvent;

class ModelValidationEvent extends ActionEvent
{

    const MODEL_VALIDATION_EVENT_PREFIX = 'open_api_model_validation_';

    /** @var BaseApiModel $model */
    protected $model;

    /** @var ModelFactory  */
    protected $modelFactory;

    protected $propertyPatchPrefix;

    /** @var array $violations  */
    protected $violations;

    /**
     * @param BaseApiModel $model
     * @param ModelFactory $modelFactory
     * @param $propertyPatchPrefix
     * @param array $violations
     */
    public function __construct(BaseApiModel $model, ModelFactory $modelFactory, string $propertyPatchPrefix = "", array $violations = [])
    {
        $this->model = $model;
        $this->modelFactory = $modelFactory;
        $this->propertyPatchPrefix = $propertyPatchPrefix;
        $this->violations = $violations;
    }


    /**
     * @return BaseApiModel
     */
    public function getModel(): BaseApiModel
    {
        return $this->model;
    }

    /**
     * @param BaseApiModel $model
     */
    public function setModel(BaseApiModel $model): void
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param array $violations
     */
    public function setViolations($violations): void
    {
        $this->violations = $violations;
    }

    /**
     * @return ModelFactory
     */
    public function getModelFactory(): ModelFactory
    {
        return $this->modelFactory;
    }

    /**
     * @param ModelFactory $modelFactory
     */
    public function setModelFactory(ModelFactory $modelFactory): void
    {
        $this->modelFactory = $modelFactory;
    }

    /**
     * @return string
     */
    public function getPropertyPatchPrefix(): string
    {
        return $this->propertyPatchPrefix;
    }

    /**
     * @param string $propertyPatchPrefix
     */
    public function setPropertyPatchPrefix(string $propertyPatchPrefix): void
    {
        $this->propertyPatchPrefix = $propertyPatchPrefix;
    }



}