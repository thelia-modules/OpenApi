<?php

namespace OpenApi\Model\Api;

/**
 * Class Result.
 *
 * @OA\Schema(
 *     description="A result (to be in search object)"
 * )
 */
class Result extends BaseApiModel
{
    /**
     * @var float
     * @OA\Property(
     *     type="number",
     *     format="float",
     *     description="The weight of result in search"
     * )
     */
    protected $weight;

    /**
     * @var string
     * @OA\Property(
     *     type="string"
     * )
     */
    protected $type;

    /**
     * @var BaseApiModel
     * @OA\Property(
     *     type="object",
     *     description="Almost any of OpenApi object"
     * )
     */
    protected $object;

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return Result
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Result
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return BaseApiModel
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param BaseApiModel $object
     *
     * @return Result
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }
}
