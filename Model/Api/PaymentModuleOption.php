<?php

namespace OpenApi\Model\Api;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\NotBlank;


#[Schema(description: "An option for payment module")]
class PaymentModuleOption extends BaseApiModel
{
    #[Property(type: "string")]
    #[NotBlank(groups: ["read"])]
    protected string $code;

    #[Property(type: "boolean")]
    protected bool $valid;

    #[Property(type: "string")]
    protected string $title;

    #[Property(type: "string")]
    protected string $description;

    #[Property(description: "Option logo url", type: "string")]
    protected string $image;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): PaymentModuleOption
    {
        $this->code = $code;
        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): PaymentModuleOption
    {
        $this->valid = $valid;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): PaymentModuleOption
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): PaymentModuleOption
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): PaymentModuleOption
    {
        $this->image = $image;
        return $this;
    }
}
