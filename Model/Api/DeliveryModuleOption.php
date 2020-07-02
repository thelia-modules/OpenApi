<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class DeliveryModuleOption
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="An option for delivery module"
 * )
 */
class DeliveryModuleOption extends BaseApiModel
{
    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $code;

    /**
     *  @OA\Property(
     *     type="boolean"
     *  )
     */
    protected $valid;

    /**
     *  @OA\Property(
     *     type="string"
     *  )
     */
    protected $title;

    /**
     *  @OA\Property(
     *     type="string",
     *     description="Delivery logo url"
     *  )
     */
    protected $image;

    /**
     *  @OA\Property(
     *     type="string",
     *     format="date-time"
     *  )
     */
    protected $minimumDeliveryDate;

    /**
     *  @OA\Property(
     *     type="string",
     *     format="date-time"
     *  )
     */
    protected $maximumDeliveryDate;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float"
     *  )
     */
    protected $postage;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float"
     *  )
     */
    protected $postageTax;

    /**
     *  @OA\Property(
     *     type="number",
     *     format="float"
     *  )
     */
    protected $postageUntaxed;
}