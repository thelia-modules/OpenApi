<?php

namespace OpenApi\EventListener;

use OpenApi\Model\Api\Address;
use OpenApi\OpenApi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\OrderAddressQuery;

class OrderListener implements EventSubscriberInterface
{
    protected $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function setPickupAddress(OrderEvent $event)
    {
        /** @var Address $pickupAddress */
        $pickupAddress = $this->request->getSession()->get(OpenApi::PICKUP_ADDRESS_SESSION_KEY);

        if (null === $pickupAddress || null === $pickupAddress->getAddress1() || null === $pickupAddress->getCity()) {
            return;
        }

        OrderAddressQuery::create()
            ->findPK($event->getOrder()->getDeliveryOrderAddressId())
            ->setCompany($pickupAddress->getCompany())
            ->setAddress1($pickupAddress->getAddress1())
            ->setAddress2($pickupAddress->getAddress2())
            ->setAddress3($pickupAddress->getAddress3())
            ->setZipcode($pickupAddress->getZipcode())
            ->setCity($pickupAddress->getCity())
            ->save();

        // Reset pickup address
        $this->request->getSession()->set(OpenApi::PICKUP_ADDRESS_SESSION_KEY, null);
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('setPickupAddress', 256),
        );
    }
}