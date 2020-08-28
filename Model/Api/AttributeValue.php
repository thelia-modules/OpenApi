<?php

namespace OpenApi\Model\Api;

use OpenApi\Model\Api\ModelTrait\translatable;

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
}