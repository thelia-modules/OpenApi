<?php

namespace OpenApi\Model\Api;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Symfony\Component\Validator\Constraints\NotBlank;


#[Schema(description: "An option choice for payment module")]
class PaymentModuleOptionChoice extends BaseApiModel
{
    #[Property(type: "string")]
    #[NotBlank(groups: ["read"])]
    protected string $group;

    #[Property(type: "array", items: new Items(type: "string"))]
    protected array $values;

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): PaymentModuleOptionChoice
    {
        $this->group = $group;
        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): PaymentModuleOptionChoice
    {
        $this->values = $values;
        return $this;
    }
}
