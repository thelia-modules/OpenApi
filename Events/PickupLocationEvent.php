<?php

namespace OpenApi\Events;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Cart;

class PickupLocationEvent extends ActionEvent
{
    public const MODULE_DELIVERY_SET_PICKUP_LOCATION = 'thelia.module.delivery.set.pickup_location';

    private ?string $id = null;

    private ?int $default = null;

    private ?string $label = null;

    private ?string $title = null;

    private ?string $address = null;

    private ?string $zipCode = null;

    private ?string $city = null;

    private ?string $countryCode = null;

    private ?string $type = null;

    private ?Cart $cart = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): PickupLocationEvent
    {
        $this->id = $id;
        return $this;
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    public function setDefault(?int $default): PickupLocationEvent
    {
        $this->default = $default;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): PickupLocationEvent
    {
        $this->label = $label;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): PickupLocationEvent
    {
        $this->title = $title;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): PickupLocationEvent
    {
        $this->address = $address;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): PickupLocationEvent
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): PickupLocationEvent
    {
        $this->city = $city;
        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): PickupLocationEvent
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): PickupLocationEvent
    {
        $this->type = $type;
        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): PickupLocationEvent
    {
        $this->cart = $cart;
        return $this;
    }

    public function setFromPayload(array $payload): PickupLocationEvent
    {
        $this
            ->setId($payload['id'] ?? null)
            ->setDefault($payload['default'] ?? null)
            ->setLabel($payload['label'] ?? null)
            ->setTitle($payload['title'] ?? null)
            ->setAddress($payload['address1'] ?? null)
            ->setZipCode($payload['zipCode'] ?? null)
            ->setCity($payload['city'] ?? null)
            ->setCountryCode($payload['countryCode'] ?? null)
            ->setType($payload['type'] ?? null)
        ;

        return $this;
    }
}