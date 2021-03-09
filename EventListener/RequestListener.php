<?php

namespace OpenApi\EventListener;

use OpenApi\Controller\Front\BaseFrontOpenApiController;
use OpenApi\OpenApi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Thelia\Core\Security\SecurityContext;

class RequestListener implements EventSubscriberInterface
{
    protected $securityContext;

    public function __construct(
        SecurityContext $securityContext
    ) {
        $this->securityContext = $securityContext;
    }

    public function markRequestAsOpenApi(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (isset($controller[0]) && $controller[0] instanceof BaseFrontOpenApiController || $controller[0] instanceof BaseFrontOpenApiController) {
            $currentRequest = $event->getRequest();
            $currentRequest->attributes->set(OpenApi::OPEN_API_ROUTE_REQUEST_KEY, true);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER
        ];
    }
}
