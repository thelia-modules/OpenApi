<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use OpenApi\Model\Api\Address as OpenApiAddress;
use OpenApi\Model\Api\Customer as OpenApiCustomer;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Propel\Runtime\Propel;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Address;
use Thelia\Model\Customer;

/**
 * @Route("/customer", name="customer")
 */
class CustomerController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="get_customer", methods="GET")
     *
     * @OA\Get(
     *     path="/customer",
     *     tags={"customer"},
     *     summary="Get current customer",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Customer"
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
    public function getCustomer(
        OpenApiService $openApiService,
        ModelFactory $modelFactory
    ) {
        $currentCustomer = $openApiService->getCurrentCustomer();

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $modelFactory->buildModel('Customer', $currentCustomer);
        $openApiCustomer->validate(self::GROUP_READ);

        return OpenApiService::jsonResponse($openApiCustomer);
    }

    /**
     * @Route("", name="add_customer", methods="POST")
     *
     * @OA\Post(
     *     path="/customer",
     *     tags={"customer"},
     *     summary="Create a new customer",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="customer",
     *                     type="object",
     *                     ref="#/components/schemas/Customer",
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="object",
     *                     ref="#/components/schemas/Address",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Customer"
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
    public function createCustomer(Request $request, ModelFactory $modelFactory)
    {
        $data = json_decode($request->getContent(), true);

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $modelFactory->buildModel('Customer', $data['customer']);
        $openApiCustomer->validate(self::GROUP_CREATE);

        /** We create a Propel transaction to save the customer and get its ID necessary for the validation
         * of the address without actually commiting to the base until everything is in order.
         */
        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            /** @var Customer $theliaCustomer */
            $theliaCustomer = $openApiCustomer->toTheliaModel();
            $theliaCustomer->setPassword($data['password'])->save();
            $openApiCustomer->setId($theliaCustomer->getId());

            /** We must catch the validation exception if it is thrown to rollback the Propel transaction before throwing the exception again */
            /** @var OpenApiAddress $openApiAddress */
            $openApiAddress = $modelFactory->buildModel('Address', $data['address']);
            $openApiAddress->setCustomer($openApiCustomer)->validate(self::GROUP_CREATE);

            /** @var Address $theliaAddress */
            $theliaAddress = $openApiAddress->toTheliaModel();
            $theliaAddress
                ->setLabel(Translator::getInstance()->trans('Main Address', [], OpenApi::DOMAIN_NAME))
                ->setIsDefault(1)
                ->save()
            ;
        } catch (\Exception $exception) {
            $con->rollBack();
            throw $exception;
        }

        /* If everything went fine, we actually commit the changes to the base. */
        $con->commit();

        $openApiCustomer->setDefaultAddressId($theliaAddress->getId());

        return OpenApiService::jsonResponse($openApiCustomer);
    }

    /**
     * @Route("", name="update_customer", methods="PATCH")
     *
     * @OA\Patch(
     *     path="/customer",
     *     tags={"customer"},
     *     summary="Edit the current customer",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="customer",
     *                     type="object",
     *                     ref="#/components/schemas/Customer",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Customer"
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
    public function updateCustomer(
        Request $request,
        SecurityContext $securityContext,
        OpenApiService $openApiService,
        ModelFactory $modelFactory
    ) {
        $currentCustomer = $openApiService->getCurrentCustomer();

        $data = json_decode($request->getContent(), true);

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $modelFactory->buildModel('Customer', $data['customer']);
        $openApiCustomer->setId($currentCustomer->getId())->validate(self::GROUP_UPDATE);

        /** @var Customer $theliaCustomer */
        $theliaCustomer = $openApiCustomer->toTheliaModel();
        $theliaCustomer->setNew(false);

        if (\array_key_exists('password', $data) && null !== $newPassword = $data['password']) {
            $theliaCustomer->setPassword($newPassword);
        }

        $theliaCustomer->save();

        $securityContext->setCustomerUser($theliaCustomer);

        return OpenApiService::jsonResponse($openApiCustomer);
    }
}
