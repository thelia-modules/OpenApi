<?php

namespace OpenApi\EventListener;

use OpenApi\Exception\OpenApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Thelia\Core\HttpFoundation\JsonResponse;

class ExceptionListener implements EventSubscriberInterface
{
    public function catchOpenApiExceptions(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof OpenApiException) {
            return;
        }

        /** @var OpenApiException $openApiException */
        $openApiException = $event->getException();

        $response = new JsonResponse($openApiException->getError(), $openApiException->getHttpCode());
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => "catchOpenApiExceptions"
        ];
    }
}