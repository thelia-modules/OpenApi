<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class PaymentModule
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A module of type payment"
 * )
 */
class PaymentModule
{
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
     *     description="Payment logo url"
     *  )
     */
    protected $image;
}