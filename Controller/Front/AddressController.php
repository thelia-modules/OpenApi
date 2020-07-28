<?php

namespace OpenApi\Controller\Front;

use OpenApi\Exception\OpenApiException;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Address;
use Thelia\Model\AddressQuery;
use OpenApi\Model\Api\Address as OpenApiAddress;

/**
 * @Route("/address", name="address")
 */
class AddressController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="get_address", methods="GET")
     *
     * @OA\Get(
     *     path="/address",
     *     tags={"address"},
     *     summary="Get current customer addresses",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Address"
     *                  )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     * )
     */
    public function getAddress(Request $request)
    {
        $currentCustomer = $this->getCurrentCustomer();

        $addresses = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->find();

        return $this->jsonResponse(
            array_map(
                function (Address $address) {
                    /** @var OpenApiAddress $openApiAddress */
                    $openApiAddress = $this->getModelFactory()->buildModel('Address', $address);
                    $openApiAddress->validate(self::GROUP_READ);
                    return ($openApiAddress);
                },
                iterator_to_array($addresses)
            )
        );
    }

    /**
     * @Route("", name="add_address", methods="POST")
     *
     * @OA\Post(
     *     path="/address",
     *     tags={"address"},
     *     summary="Add an address to current customer",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Address"
     *                  )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function createAddress(Request $request)
    {
        $currentCustomer = $this->getCurrentCustomer();

        /** @var OpenApiAddress $openApiAddress */
        $openApiAddress = $this->getModelFactory()->buildModel('Address', $request->getContent());
        $openApiAddress->validate(self::GROUP_CREATE);

        /** @var Address $theliaAddress */
        $theliaAddress = $openApiAddress->toTheliaModel();

        $theliaAddress->setCustomerId($currentCustomer->getId())
            ->save();

        return $this->jsonResponse($openApiAddress);
    }

    /**
     * @Route("/{id}", name="update_address", methods="PATCH")
     *
     * @OA\Patch(
     *     path="/address/{id}",
     *     tags={"address"},
     *     summary="Update address",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Address")
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function updateAddress(Request $request, $id)
    {
        $currentCustomer = $this->getCurrentCustomer();

        $address = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->filterById($id)
            ->findOne();

        if (null === $address) {
            /** @var Error $error */
            $error = $this->getModelFactory()->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans("No address found for id $id", [], OpenApi::DOMAIN_NAME),
                ]
            );

            throw new OpenApiException($error);
        }

        /** @var OpenApiAddress $openApiAddress */
        $openApiAddress = $this->getModelFactory()->buildModel('Address', $request->getContent())
            ->setCustomer($this->getModelFactory()->buildModel('Customer', $currentCustomer))
            ->validate(self::GROUP_UPDATE);

        $openApiAddress->toTheliaModel();
        $address->save();

        return new JsonResponse($openApiAddress);
    }

    /**
     * @Route("/{id}", name="delete_address", methods="DELETE")
     *
     * @OA\Delete(
     *     path="/address/{id}",
     *     tags={"address"},
     *     summary="Delete address",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *          response="204",
     *          description="Success"
     *     )
     * )
     */
    public function deleteAddress(Request $request, $id)
    {
        $currentCustomer = $this->getCurrentCustomer();

        $address = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->filterById($id)
            ->findOne();

        if (null === $address) {
            /** @var Error $error */
            $error = $this->getModelFactory()->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans("No address found for id $id", [], OpenApi::DOMAIN_NAME),
                ]
            );

            throw new OpenApiException($error);
        }

        $address->delete();

        return new JsonResponse("", 204);
    }
}