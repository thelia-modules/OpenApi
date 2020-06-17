<?php

namespace OpenApi\Model\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Address",
 *     title="Address",
 *     description="Address model"
 * )
 */
class Address
{
    /**
     * @OA\Property(
     *    type="integer"
     * )
     */
    protected $id;

    /**
     * @OA\Property(
     *    type="boolean"
     * )
     */
    protected $isDefault;

    /**
     * @OA\Property(
     *     type="string",
     *     description="The name for this address",
     * )
     */
    protected $label;

    /**
     * @OA\Property(
     *     type="string",
     *     description="The civility of the person",
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $firstName;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $lastName;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $cellphoneNumber;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $phoneNumber;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $company;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address1;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address2;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $address3;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $zipCode;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $city;

    /**
     * @OA\Property(
     *     type="string",
     *     description="Country ISO 3166-1 alpha-2 code"
     * )
     */
    protected $countryCode;

    /**
     * @OA\Property(
     *     type="object"
     * )
     */
    protected $additionalData;
}