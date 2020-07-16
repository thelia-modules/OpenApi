<?php

namespace OpenApi\Controller\Front;

use Doctrine\Common\Annotations\AnnotationRegistry;
use OpenApi\Annotations as OA;
use OpenApi\Exception\OpenApiException;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use Thelia\Controller\BaseController;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CustomerQuery;

abstract class BaseFrontOpenApiController extends BaseFrontController
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");

        $loader = require THELIA_VENDOR.'autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    /**
     * @param bool $throwExceptionIfNull
     *
     * @return \Thelia\Model\Customer|null
     * @throws OpenApiException
     */
    protected function getCurrentCustomer($throwExceptionIfNull = true)
    {
        $currentCustomer = $this->getSecurityContext()->getCustomerUser();

        if (null === $currentCustomer && $throwExceptionIfNull) {
            throw new OpenApiException(
                (
                new Error(
                    Translator::getInstance()->trans("Invalid data", [], OpenApi::DOMAIN_NAME),
                    Translator::getInstance()->trans("No customer found", [], OpenApi::DOMAIN_NAME)
                )
                )
            );
        }

        return $currentCustomer;
    }
}