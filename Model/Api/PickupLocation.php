<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="PickupLocation",
 *     description="PickupLocation model"
 * )
 */
class PickupLocation extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer"
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="float"
     * )
     */
    protected $latitude;

    /**
     * @OA\Property(
     *    type="float"
     * )
     */
    protected $longitude;

    /**
     * @OA\Property(
     *     type="string"
     * )
     */
    protected $title;

    /**
     * @var Address
     * @OA\Property(
     *     ref="#/components/schemas/Address"
     * )
     */
    protected $address;
}