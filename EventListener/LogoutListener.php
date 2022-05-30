<?php

namespace OpenApi\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\TheliaEvents;

class LogoutListener implements EventSubscriberInterface
{
    protected $request;

    /**
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }


    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::CUSTOMER_LOGOUT => ['emptyOrderSession', 30]
        ];
    }

    public function emptyOrderSession()
    {
        $this->request->getSession()->set('thelia.order', null);
    }
}