<?php

namespace OpenApi\EventListener;

use OpenApi\Exception\OpenApiException;
use OpenApi\Model\Api\Error;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;

class ExceptionListener implements EventSubscriberInterface
{
    /** @var ModelFactory */
    protected $modelFactory;

    public function __construct(ModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    /**
     * Convert all exception to OpenApiException if route is an open api route.
     */
    public function catchAllException(ExceptionEvent $event): void
    {
        // Do nothing if this is already an Open Api Exception
        if ($event->getThrowable() instanceof OpenApiException) {
            return;
        }

        // Do nothing on non-api routes
        if (!$event->getRequest()->attributes->get(OpenApi::OPEN_API_ROUTE_REQUEST_KEY, false)) {
            return;
        }

        Tlog::getInstance()->error($event->getThrowable()->getTraceAsString());

        /** @var Error $error */
        $error = $this->modelFactory->buildModel(
            'Error',
            [
                'title' => Translator::getInstance()->trans('Unexpected error', [], OpenApi::DOMAIN_NAME),
                'description' => $event->getThrowable()->getMessage(),
            ]
        );

        $event->setThrowable((new OpenApiException($error)));
    }

    /**
     * Format OpenApiException to JSON response.
     */
    public function catchOpenApiException(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof OpenApiException) {
            return;
        }

        /** @var OpenApiException $openApiException */
        $openApiException = $event->getThrowable();

        $response = new JsonResponse($openApiException->getError(), $openApiException->getHttpCode());
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['catchOpenApiException', 256],
                ['catchAllException', 512],
            ],
        ];
    }
}
