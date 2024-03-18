<?php

namespace OpenApi\Model\Api;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Schema(description: "A group of option for payment module")]
class PaymentModuleOptionGroup extends BaseApiModel
{
    #[Property(type: "string")]
    #[NotBlank(groups: ["read"])]
    protected string $code;

    #[Property(type: "integer")]
    protected ?int $minimumSelectedOptions;

    #[Property(type: "integer")]
    protected ?int $maximumSelectedOptions;

    #[Property(type: "string")]
    protected string $title;

    #[Property(type: "string")]
    protected ?string $description = null;

    #[Property(
        type: "array",
        items: new Items(ref: "#/components/schemas/PaymentModuleOption")
    )]
    protected array $options;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): PaymentModuleOptionGroup
    {
        $this->code = $code;
        return $this;
    }

    public function getMinimumSelectedOptions(): ?int
    {
        return $this->minimumSelectedOptions;
    }

    public function setMinimumSelectedOptions(?int $minimumSelectedOptions): PaymentModuleOptionGroup
    {
        $this->minimumSelectedOptions = $minimumSelectedOptions;
        return $this;
    }

    public function getMaximumSelectedOptions(): ?int
    {
        return $this->maximumSelectedOptions;
    }

    public function setMaximumSelectedOptions(?int $maximumSelectedOptions): PaymentModuleOptionGroup
    {
        $this->maximumSelectedOptions = $maximumSelectedOptions;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): PaymentModuleOptionGroup
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): PaymentModuleOptionGroup
    {
        $this->description = $description;
        return $this;
    }

    public function appendPaymentModuleOption(PaymentModuleOption $paymentModuleOption)
    {
        $this->options[] = $paymentModuleOption;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): PaymentModuleOptionGroup
    {
        $this->options = $options;
        return $this;
    }
}
