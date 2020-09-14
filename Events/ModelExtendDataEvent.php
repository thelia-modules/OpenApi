<?php

namespace OpenApi\Events;

use OpenApi\Model\Api\BaseApiModel;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Event\TheliaEvents;

class ModelExtendDataEvent extends ActionEvent
{
    const ADD_EXTEND_DATA_PREFIX = "add_extend_data_";

    protected $data;

    protected $locale;

    /** @var BaseApiModel */
    protected $model;

    protected $extendedData;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return ModelExtendDataEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     *
     * @return ModelExtendDataEvent
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return BaseApiModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param BaseApiModel $model
     *
     * @return ModelExtendDataEvent
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtendData()
    {
        return $this->extendedData;
    }


    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setExtendDataKeyValue($key, $value)
    {
        $this->extendedData[$key] = $value;
        return $this;
    }


}