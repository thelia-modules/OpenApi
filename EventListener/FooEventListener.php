<?php

namespace OpenApi\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Delivery\PickupLocationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\PickupLocation;
use Thelia\Model\PickupLocationAddress;

class FooEventListener implements EventSubscriberInterface
{
    public function setLocations(PickupLocationEvent $locationEvent)
    {
        $locationEvent->appendLocation(
            (new PickupLocation())
                ->setId(1)
                ->setTitle('First test')
                ->setLatitude(42)
                ->setLongitude(51)
                ->setAddress(
                    (new PickupLocationAddress())
                        ->setId(1)
                        ->setIsDefault(true)
                        ->setLabel("label")
                        ->setTitle('Mr')
                        ->setFirstName("vincent")
                        ->setLastName("lopes")
                        ->setCellphoneNumber("000")
                        ->setPhoneNumber("066")
                        ->setCompany("OS")
                        ->setAddress1("Add1")
                        ->setAddress2("Add2")
                        ->setAddress3("Add3")
                        ->setZipCode("63000")
                        ->setCity("Clermont")
                        ->setCountryCode("FR")
                        ->setAdditionalData(['test' => 'ok'])
                )
        );

        $locationEvent->appendLocation(
            (new PickupLocation())
                ->setId(2)
                ->setTitle('Second test')
                ->setLatitude(42)
                ->setLongitude(51)
                ->setOpeningHours(PickupLocation::MONDAY_OPENING_HOURS_KEY, '09:00-12:00 14:00-18:00')
                ->setOpeningHours(PickupLocation::TUESDAY_OPENING_HOURS_KEY, '09:00-12:00 14:00-18:00')
                ->setOpeningHours(PickupLocation::WEDNESDAY_OPENING_HOURS_KEY, '09:00-12:00 14:00-18:00')
                ->setOpeningHours(PickupLocation::THURSDAY_OPENING_HOURS_KEY, '09:00-12:00 14:00-18:00')
                ->setOpeningHours(PickupLocation::FRIDAY_OPENING_HOURS_KEY, '09:00-12:00 14:00-18:00')
                ->setOpeningHours(PickupLocation::SATURDAY_OPENING_HOURS_KEY, '09:00-12:00 00:00-00:00')
                ->setOpeningHours(PickupLocation::SUNDAY_OPENING_HOURS_KEY, '00:00-00:00 00:00-00:00')
                ->setAddress(
                    (new PickupLocationAddress())
                        ->setId(2)
                        ->setIsDefault(true)
                        ->setLabel("label")
                    ->setTitle('Mr')
                    ->setFirstName("vincent")
                    ->setLastName("lopes")
                    ->setCellphoneNumber("000")
                    ->setPhoneNumber("066")
                    ->setCompany("OS")
                    ->setAddress1("Add1")
                    ->setAddress2("Add2")
                    ->setAddress3("Add3")
                    ->setZipCode("63000")
                    ->setCity("Clermont")
                    ->setCountryCode("FR")
                    ->setAdditionalData(['test' => 'ok'])
                )
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::MODULE_DELIVERY_GET_PICKUP_LOCATIONS => 'setLocations'
        ];
    }
}