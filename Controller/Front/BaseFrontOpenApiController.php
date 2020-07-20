<?php

namespace OpenApi\Controller\Front;

use Doctrine\Common\Annotations\AnnotationRegistry;
use OpenApi\Exception\OpenApiException;
use OpenApi\Model\Api\Error;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\Translation\Translator;

abstract class BaseFrontOpenApiController extends BaseFrontController
{
    /** @var ModelFactory */
    private $modelFactory;

    const GROUP_CREATE = 'create';

    const GROUP_READ = 'read';

    const GROUP_UPDATE = 'update';

    const GROUP_DELETE = 'delete';

    public function __construct()
    {
        $loader = require THELIA_VENDOR.'autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    public function jsonResponse($data, $code = 200)
    {
        $response = (new JsonResponse())
            ->setContent(json_encode(data));

        // TODO : Add more flexibility to CORS check
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @param bool $throwExceptionIfNull
     *
     * @return \Thelia\Model\Customer
     * @throws OpenApiException
     */
    protected function getCurrentCustomer($throwExceptionIfNull = true)
    {
        $currentCustomer = $this->getSecurityContext()->getCustomerUser();

        if (null === $currentCustomer && $throwExceptionIfNull) {
            /** @var Error $error */
            $error = $this->modelFactory->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans("No customer found", [], OpenApi::DOMAIN_NAME),
                ]
            );
            throw new OpenApiException($error);
        }

        return $currentCustomer;
    }

    /**
     * @param bool $throwExceptionIfNull
     *
     * @return \Thelia\Model\Cart
     * @throws OpenApiException
     */
    protected function getSessionCart($throwExceptionIfNull = true)
    {
        $cart = $this->getRequest()->getSession()->getSessionCart($this->getDispatcher());

        if (null === $cart && $throwExceptionIfNull) {
            /** @var Error $error */
            $error = $this->modelFactory->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans("No cart found", [], OpenApi::DOMAIN_NAME),
                ]
            );
            throw new OpenApiException($error);
        }

        return $cart;
    }

    protected function getModelFactory()
    {
        if (null === $this->modelFactory) {
            $this->modelFactory = $this->getContainer()->get('open_api.model.factory');
        }

        return $this->modelFactory;
    }
}