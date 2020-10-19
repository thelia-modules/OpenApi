<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\translatable;
use Thelia\Model\AttributeAv;
use Thelia\Model\Base\AttributeAvQuery;

/**
 * Class AttributeValue
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An attribute value"
 * )
 */
class AttributeValue extends BaseApiModel
{
    use translatable;

    /**
     * @var integer
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @param AttributeAv $attributeAv
     * @return $this
     */
    public function fillFromTheliaAttributeAv(AttributeAv $attributeAv)
    {
        $this->id = $attributeAv->getId();
        $this->setTitle($attributeAv->setLocale($this->locale)->getTitle());
        $this->setDescription($attributeAv->setLocale($this->locale)->getDescription());
        $this->setChapo($attributeAv->setLocale($this->locale)->getChapo());
        $this->setPostscriptum($attributeAv->setLocale($this->locale)->getPostscriptum());

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AttributeValue
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * "setAttributeId" alias to fit Thelia model

     * @param int $id
     * @return AttributeValue
     */
    public function setAttributeId($id)
    {
        return $this->setId($id);
    }
}