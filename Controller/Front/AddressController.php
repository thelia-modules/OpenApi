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
use OpenApi\Model\Api\Customer as OpenApiCustomer;

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

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $this->getModelFactory()->buildModel('Customer', $currentCustomer);

        /** @var OpenApiAddress $openApiAddress */
        $openApiAddress = $this->getModelFactory()->buildModel('Address', $request->getContent());
        $openApiAddress
            ->setCustomer($openApiCustomer)
            ->validate(self::GROUP_CREATE);

        $openApiAddress->getLabel() ?: $openApiAddress->setLabel(Translator::getInstance()->trans('Main Address'));

        /** @var Address $theliaAddress */
        $theliaAddress = $openApiAddress->toTheliaModel();
        $theliaAddress->save();

        $oldDefaultAddress = AddressQuery::create()->filterByCustomer($currentCustomer)->filterByIsDefault(true)->findOne();
        if (null === $oldDefaultAddress || $openApiAddress->getIsDefault()) {
            $theliaAddress->makeItDefault();
        }

        return $this->jsonResponse($openApiAddress->setId($theliaAddress->getId()));
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

        $theliaAddress = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->filterById($id)
            ->findOne();

        if (null === $theliaAddress) {
            /** @var Error $error */
            $error = $this->getModelFactory()->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans("No address found for id $id for the current customer.", [], OpenApi::DOMAIN_NAME),
                ]
            );

            throw new OpenApiException($error);
        }

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $this->getModelFactory()->buildModel('Customer', $currentCustomer);

        /** @var OpenApiAddress $openApiAddress */
        $openApiAddress = $this->getModelFactory()->buildModel('Address', $request->getContent());
        $openApiAddress
            ->setId($id)
            ->setCustomer($openApiCustomer)
            ->validate(self::GROUP_UPDATE);

        /** @var Address $address */
        $theliaAddress = $openApiAddress->toTheliaModel();

        $oldDefaultAddress = AddressQuery::create()->filterByCustomer($currentCustomer)->filterByIsDefault(true)->findOne();
        if (null === $oldDefaultAddress || $openApiAddress->isDefault()) {
            $theliaAddress->makeItDefault();
        }

        $theliaAddress->save();

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
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function deleteAddress(Request $request, $id)
    {
        $currentCustomer = $this->getCurrentCustomer();

        $theliaAddress = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->filterById($id)
            ->findOne();

        if (null === $theliaAddress || $theliaAddress->getIsDefault()) {
            $errorDescription = $theliaAddress ? "Impossible to delete the default address." : "No address found for id $id for the current customer.";
            /** @var Error $error */
            $error = $this->getModelFactory()->buildModel(
                'Error',
                [
                    'title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                    'description' => Translator::getInstance()->trans($errorDescription, [], OpenApi::DOMAIN_NAME),
                ]
            );

            throw new OpenApiException($error);
        }

        $theliaAddress->delete();

        return new JsonResponse("Success", 204);
    }
}