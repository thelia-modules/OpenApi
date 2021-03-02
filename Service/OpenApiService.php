<?php

namespace OpenApi\Service;

use OpenApi\Exception\OpenApiException;
use OpenApi\Model\Api\Error;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Translation\Translator;

class OpenApiService
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Request
     */
    protected $currentRequest;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        SecurityContext $securityContext,
        ModelFactory $modelFactory,
        RequestStack $requestStack,
        EventDispatcherInterface $dispatcher
    ) {

        $this->securityContext = $securityContext;
        $this->modelFactory = $modelFactory;
        $this->requestStack = $requestStack;
        $this->currentRequest = $requestStack->getCurrentRequest();
        $this->dispatcher = $dispatcher;
    }

    public static function jsonResponse($data, $code = 200, $cors = "*")
    {
        $response = (new JsonResponse())
            ->setContent(json_encode($data));

        $response->headers->set('Access-Control-Allow-Origin', $cors);
        $response->setStatusCode($code);
        return $response;
    }

    /**
     * @param bool $throwExceptionIfNull
     *
     * @return \Thelia\Model\Customer
     * @throws OpenApiException
     */
    public function getCurrentCustomer($throwExceptionIfNull = true)
    {
        $currentCustomer = $this->securityContext->getCustomerUser();

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
    public function getSessionCart($throwExceptionIfNull = true)
    {
        $cart = $this->currentRequest->getSession()->getSessionCart($this->dispatcher);

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

    public function getRequestValue($key, $default = null)
    {
        $requestData = json_decode($this->currentRequest->getContent(), true);

        if (!isset($requestData[$key]) || null === $requestData[$key]) {
            return $default;
        }

        return $requestData[$key];
    }

    public function buildOpenApiException($title, $description = ""): OpenApiException
    {
        /** @var Error $error */
        $error = $this->modelFactory->buildModel(
            'Error',
            [
                'title' => $title,
                'description' => $description,
            ]
        );

        return new OpenApiException($error);
    }

    public  function getCurrentOpenApiCart()
    {
        $cart = $this->currentRequest->getSession()->getSessionCart($this->dispatcher);

        return $this->modelFactory->buildModel('Cart', $cart);
    }
}
