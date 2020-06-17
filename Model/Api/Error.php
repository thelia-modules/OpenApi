<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * Class Error
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="A module of type payment"
 * )
 */
class Error
{
    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *    type="string"
     * )
     */
    protected $description;

    /**
     * @OA\Property(
     *    type="array",
     *     @OA\Items(
     *          type="object",
     *          @OA\Property(
     *              property="label",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="error",
     *              type="string"
     *          )
     *     )
     * )
     */
    protected $violations;
}