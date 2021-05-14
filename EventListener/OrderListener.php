<?php

namespace OpenApi\EventListener;

use OpenApi\Model\Api\Address;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\OrderAddressQuery;

class OrderListener implements EventSubscriberInterface
{
    protected $request;

    protected $modelFactory;

    public function __construct(RequestStack $requestStack, ModelFactory $modelFactory)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->modelFactory = $modelFactory;
    }

    public function setPickupAddress(OrderEvent $event): void
    {
        /** @var Address $pickupAddress */
        $pickupAddressJson = $this->request->getSession()->get(OpenApi::PICKUP_ADDRESS_SESSION_KEY);
        $pickupAddress = $this->modelFactory->buildModel('Address', $pickupAddressJson);

        if (null === $pickupAddress || null === $pickupAddress->getAddress1() || null === $pickupAddress->getCity()) {
            return;
        }

        $orderAddress = OrderAddressQuery::create()
            ->findPK($event->getOrder()->getDeliveryOrderAddressId())
            ->setCompany($pickupAddress->getCompany())
            ->setAddress1($pickupAddress->getAddress1())
            ->setAddress2($pickupAddress->getAddress2())
            ->setAddress3($pickupAddress->getAddress3())
            ->setZipcode($pickupAddress->getZipcode())
            ->setCity($pickupAddress->getCity())
        ;
        $orderAddress->save();
        $event->setDeliveryAddress($orderAddress->getId());

        // Reset pickup address
        $this->request->getSession()->set(OpenApi::PICKUP_ADDRESS_SESSION_KEY, null);
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::ORDER_BEFORE_PAYMENT => ['setPickupAddress', 256],
        ];
    }
}
