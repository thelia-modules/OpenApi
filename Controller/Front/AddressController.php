<?php

namespace OpenApi\Controller\Front;

use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
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
    public function getAddress(
        OpenApiService $openApiService,
        ModelFactory $modelFactory
    ) {
        $currentCustomer = $openApiService->getCurrentCustomer();

        $addresses = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->find();

        return OpenApiService::jsonResponse(
            array_map(
                function (Address $address) use ($modelFactory) {
                    /** @var OpenApiAddress $openApiAddress */
                    $openApiAddress = $modelFactory->buildModel('Address', $address);
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
    public function createAddress(
        Request $request,
        OpenApiService $openApiService,
        ModelFactory $modelFactory
    ) {
        $currentCustomer = $openApiService->getCurrentCustomer();

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $modelFactory->buildModel('Customer', $currentCustomer);

        /** @var OpenApiAddress $openApiAddress */
        $openApiAddress = $modelFactory->buildModel('Address', $request->getContent());
        $openApiAddress
            ->setCustomer($openApiCustomer)
            ->validate(self::GROUP_CREATE);

        $openApiAddress->getLabel() ?: $openApiAddress->setLabel(Translator::getInstance()->trans('Main Address'));

        /** @var Address $theliaAddress */
        $theliaAddress = $openApiAddress->toTheliaModel();

        $oldDefaultAddress = AddressQuery::create()->filterByCustomer($currentCustomer)->filterByIsDefault(true)->findOne();
        if (null === $oldDefaultAddress || $openApiAddress->getIsDefault()) {
            $theliaAddress->makeItDefault();
        }

        $theliaAddress->save();

        return OpenApiService::jsonResponse($openApiAddress->setId($theliaAddress->getId()));
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
    public function updateAddress(
        Request $request,
        OpenApiService $openApiService,
        ModelFactory $modelFactory,
        $id
    ) {
        $currentCustomer = $openApiService->getCurrentCustomer();

        $theliaAddress = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->filterById($id)
            ->findOne();

        if (null === $theliaAddress) {
            throw $openApiService->buildOpenApiException(
                Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                Translator::getInstance()->trans(Translator::getInstance()->trans("No address found for id $id for the current customer.", [], OpenApi::DOMAIN_NAME), [], OpenApi::DOMAIN_NAME)
            );
        }

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $modelFactory->buildModel('Customer', $currentCustomer);

        /** @var OpenApiAddress $openApiAddress */
        $openApiAddress = $modelFactory->buildModel('Address', $request->getContent());
        $openApiAddress
            ->setId($id)
            ->setCustomer($openApiCustomer)
            ->validate(self::GROUP_UPDATE);

        /** @var Address $theliaAddress */
        $theliaAddress = $openApiAddress->toTheliaModel();

        $oldDefaultAddress = AddressQuery::create()->filterByCustomer($currentCustomer)->filterByIsDefault(true)->findOne();
        $alreadyDefault = false;

        /*
         * Force a default address to stay as default
         * Because we can't unset a default address, this is only done when a new address is set as default
         */
        if (null !== $oldDefaultAddress && $oldDefaultAddress->getId() === $theliaAddress->getId()) {
            $alreadyDefault = true;
            $theliaAddress->setIsDefault(true);
        }

        if ((null === $oldDefaultAddress || $openApiAddress->getIsDefault()) && !$alreadyDefault) {
            $theliaAddress->makeItDefault();
        }

        $theliaAddress
            ->save();

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
    public function deleteAddress(
        OpenApiService $openApiService,
        ModelFactory $modelFactory,
        $id
    ) {
        $currentCustomer = $openApiService->getCurrentCustomer();

        $theliaAddress = AddressQuery::create()
            ->filterByCustomerId($currentCustomer->getId())
            ->filterById($id)
            ->findOne();

        if (null === $theliaAddress || $theliaAddress->getIsDefault()) {
            $errorDescription = $theliaAddress ? "Impossible to delete the default address." : "No address found for id $id for the current customer.";
            throw $openApiService->buildOpenApiException(
                Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME),
                Translator::getInstance()->trans($errorDescription, [], OpenApi::DOMAIN_NAME)
            );
        }

        $theliaAddress->delete();

        return new JsonResponse("Success", 204);
    }
}
