<?php

namespace OpenApi\Controller\Front;

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
        try {
            $currentCustomer = $this->getSecurityContext()->getCustomerUser();
            if (null === $currentCustomer) {
                throw new \Exception(Translator::getInstance()->trans("No customer found", [], OpenApi::DOMAIN_NAME));
            }

            $lang = $request->getSession()->getLang();

            $addresses = AddressQuery::create()
                ->filterByCustomerId($currentCustomer->getId())
                ->find();

            return new JsonResponse(
                array_map(
                    function (Address $address) use ($lang) {
                        return (new OpenApiAddress())->createFromTheliaAddress($address, $lang->getLocale());
                    },
                    iterator_to_array($addresses)
                )
            );
        } catch (\Exception $e) {
            $error = new Error(
                Translator::getInstance()->trans('Error for retrieving customer addresses', [], OpenApi::DOMAIN_NAME),
                $e->getMessage()
            );

            return new JsonResponse(
                $error,
                400
            );
        }
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
        try {
            $currentCustomer = $this->getSecurityContext()->getCustomerUser();
            if (null === $currentCustomer) {
                throw new \Exception(Translator::getInstance()->trans("No customer found", [], OpenApi::DOMAIN_NAME));
            }

            $openApiAddress = (new OpenApiAddress())->createFromRequest($request);
            $theliaAddress = $openApiAddress->toTheliaAddress();
            $theliaAddress->setCustomer($currentCustomer)
                ->save();

            return new JsonResponse($openApiAddress);
        } catch (\Exception $e) {
            $error = new Error(
                Translator::getInstance()->trans('Error for retrieving customer addresses', [], OpenApi::DOMAIN_NAME),
                $e->getMessage()
            );

            return new JsonResponse(
                $error,
                400
            );
        }
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
        try {
            $currentCustomer = $this->getSecurityContext()->getCustomerUser();
            if (null === $currentCustomer) {
                throw new \Exception(Translator::getInstance()->trans("No customer found", [], OpenApi::DOMAIN_NAME));
            }

            $address = AddressQuery::create()
                ->filterByCustomerId($currentCustomer->getId())
                ->filterById($id)
                ->findOne();

            if (null === $address) {
                throw new \Exception(Translator::getInstance()->trans("This address does not belong to this customer", [], OpenApi::DOMAIN_NAME));
            }

            $openApiAddress = (new OpenApiAddress())->createFromRequest($request)
                ->setId($id);

            $openApiAddress->toTheliaAddress()->save();

            return new JsonResponse($openApiAddress);
        } catch (\Exception $e) {
            $error = new Error(
                Translator::getInstance()->trans('Error for retrieving customer addresses', [], OpenApi::DOMAIN_NAME),
                $e->getMessage()
            );

            return new JsonResponse(
                $error,
                400
            );
        }
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
        try {
            $currentCustomer = $this->getSecurityContext()->getCustomerUser();
            if (null === $currentCustomer) {
                throw new \Exception(Translator::getInstance()->trans("No customer found", [], OpenApi::DOMAIN_NAME));
            }

            $address = AddressQuery::create()
                ->filterByCustomerId($currentCustomer->getId())
                ->filterById($id)
                ->findOne();

            if (null === $address) {
                throw new \Exception(Translator::getInstance()->trans("This address does not belong to this customer", [], OpenApi::DOMAIN_NAME));
            }

            $openApiAddress = (new OpenApiAddress())->createFromRequest($request)
                ->setId($id);

            $openApiAddress->toTheliaAddress()->delete();

            return new JsonResponse("", 204);
        } catch (\Exception $e) {
            $error = new Error(
                Translator::getInstance()->trans('Error for retrieving customer addresses', [], OpenApi::DOMAIN_NAME),
                $e->getMessage()
            );

            return new JsonResponse(
                $error,
                400
            );
        }
    }
}