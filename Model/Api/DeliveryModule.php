<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class DeliveryModule
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A module of type delivery"
 * )
 */
class DeliveryModule
{
    /**
     *  @OA\Property(
     *     type="string",
     *     enum={"pickup", "delivery"}
     *  )
     */
    protected $type;

    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $id;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $title;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $description;

    /**
     *  @OA\Property(
     *     type="string",
     *     description="Delivery logo url"
     *  )
     */
    protected $image;
}