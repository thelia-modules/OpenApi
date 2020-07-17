<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;
use OpenApi\Constraint as Constraint;

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
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @OA\Property(
     *     type="number",
     *     format="float",
     * )
     */
    protected $latitude;

    /**
     * @OA\Property(
     *     type="number",
     *     format="float",
     * )
     */
    protected $longitude;

    /**
     * @OA\Property(
     *     type="string",
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