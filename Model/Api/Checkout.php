<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class Checkout
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     schema="Checkout",
 *     title="Checkout",
 *     description="Checkout model",
 * )
 */
class Checkout
{
    /**
     *  @OA\Property(
     *     property="state",
     *     type="string",
     *     enum={"INCOMPLETE", "INVALID", "VALID"}
     *  )
     */
    protected $state;

    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $cartId;

    /**
     *  @OA\Property(
     *     type="integer",
     *     description="id of the delivery module used by this checkout"
     *  )
     */
    protected $deliveryModuleId;

    /**
     *  @OA\Property(
     *     type="integer",
     *     description="id of the payment module used by this checkout"
     *  )
     */
    protected $paymentModuleId;

    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $billingAddressId;

    /**
     *  @OA\Property(
     *     type="integer"
     *  )
     */
    protected $deliveryAddressId;

}