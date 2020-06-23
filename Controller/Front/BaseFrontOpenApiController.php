<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use Thelia\Controller\BaseController;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\CustomerQuery;

abstract class BaseFrontOpenApiController extends BaseFrontController
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
    }
}