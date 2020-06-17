<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use Thelia\Controller\BaseController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\CustomerQuery;

abstract class BaseOpenApiController extends BaseController
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
    }

    public function getControllerType()
    {
        // TODO: Implement getControllerType() method.
    }

    protected function getParser($template = null)
    {
        // TODO: Implement getParser() method.
    }

    protected function render($templateName, $args = array(), $status = 200)
    {
        // TODO: Implement render() method.
    }

    protected function renderRaw($templateName, $args = array(), $templateDir = null)
    {
        // TODO: Implement renderRaw() method.
    }
}