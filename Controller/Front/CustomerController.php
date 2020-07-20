<?php


namespace OpenApi\Controller\Front;


use OpenApi\Model\Api\Address as OpenApiAddress;
use OpenApi\Model\Api\Customer as OpenApiCustomer;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Model\Address;
use Thelia\Model\CartItem;
use Thelia\Model\Customer;
use Thelia\Model\Product;

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
    public function getCustomer(Request $request)
    {
        try {
            $currentCustomer = $this->getCurrentCustomer();

            /** @var OpenApiCustomer $openApiCustomer */
            $openApiCustomer = $this->getModelFactory()->buildModel('Customer', $data['customer']);
            $openApiCustomer->validate(self::GROUP_READ);

            return new JsonResponse((new OpenApiCustomer())->createFromTheliaCustomer($currentCustomer)->validate(self::GROUP_READ), 200);
        } catch (\Exception$exception) {
            return new JsonResponse(
                new Error(
                    Translator::getInstance()->trans('Error while retrieving customer informations.', [], OpenApi::DOMAIN_NAME),
                    $exception->getMessage()
                ),
                400
            );
        }
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
    public function createCustomer(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);

            /** @var OpenApiCustomer $openApiCustomer */
            $openApiCustomer = $this->getModelFactory()->buildModel('Customer', $data['customer']);
            $openApiCustomer->validate(self::GROUP_CREATE);

            /** @var OpenApiAddress $openApiAddress */
            $openApiAddress = $this->getModelFactory()->buildModel('Address', $data['address']);
            $openApiAddress->setCustomer($openApiCustomer)->validate(self::GROUP_CREATE);

            /** @var Customer $theliaCustomer */
            $theliaCustomer = $openApiCustomer->toTheliaModel();
            $theliaCustomer->setPassword($data['password'])->save();

            /** @var Address $theliaAddress */
            $theliaAddress = $openApiAddress->toTheliaModel();
            $theliaAddress
                ->setLabel(Translator::getInstance()->trans('Main Address', [], OpenApi::DOMAIN_NAME))
                ->setIsDefault(1)
                ->save()
            ;

            return $this->jsonResponse($openApiCustomer);
        } catch (\Exception $exception) {
            return new JsonResponse(
                new Error(
                    Translator::getInstance()->trans('Error while creating a new customer.', [], OpenApi::DOMAIN_NAME),
                    $exception->getMessage()
                ),
                400
            );
        }
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
    public function updateCustomer(Request $request)
    {
        try {
            $currentCustomer = $this->getCurrentCustomer();

            $test = $this->getModelFactory()->buildModel('Customer', null);
            $test->createFromTheliaModel($currentCustomer);

            /** @var OpenApiCustomer $openApiCustomer */
            $openApiCustomer = $this->getModelFactory()->buildModel('Customer', $request->getContent());
            $openApiCustomer->validate(self::GROUP_UPDATE);

            /** @var Customer $theliaCustomer */
            $theliaCustomer = $openApiCustomer->toTheliaModel();
            $theliaCustomer->save();

            return new JsonResponse((new OpenApiCustomer())->createFromTheliaCustomer($theliaCustomer), 200);

            //todo
            $event = $this->createCustomerUpdateEvent($request->getContent());
            $event->setCustomer($currentCustomer);

            $this->dispatch(TheliaEvents::CUSTOMER_UPDATEPROFILE, $event);

            return new JsonResponse((new OpenApiCustomer())->createFromTheliaCustomer($event->getCustomer()), 200);
        } catch (\Exception $exception) {
            return new JsonResponse(
                new Error(
                    Translator::getInstance()->trans('Error while updating customer.', [], OpenApi::DOMAIN_NAME),
                    $exception->getMessage()
                ),
                400
            );
        }
    }

    protected function createCustomerUpdateEvent($json)
    {
        $data = json_decode($json, true);

        return new CustomerCreateOrUpdateEvent(
            isset($data['customer']['civilityTitle']['id']) ? $data['customer']['civilityTitle']['id'] : null,
            isset($data['customer']['firstname']) ? $data['customer']['firstname'] : null,
            isset($data['customer']['lastname']) ? $data['customer']['lastname'] : null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            isset($data['customer']['email']) ? $data['customer']['email'] : null,
            isset($data['password']) ? $data['password'] : null,
            isset($data['customer']['lang']['id']) ? $data['customer']['lang']['id'] : null,
            isset($data['customer']['reseller']) ? $data['customer']['reseller'] : null,
            null,
            isset($data['customer']['discount']) ? $data['customer']['discount'] : null,
            null,
            null
        );
    }

    protected function createCustomerCreateEvent(OpenApiCustomer $customer, $password)
    {
        return new CustomerCreateOrUpdateEvent(
            $data['customer']['civilityTitle']['id'],
            $customer->getFirstname(),
            $customer->getLastname(),
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $customer->getEmail(),
            $password,
            $data['customer']['lang']['id'],
            $customer->getReseller(),
            null,
            $customer->getDiscount(),
            null,
            null
        );
    }

    protected function OLDcreateCustomerCreateEvent($json)
    {
        $data = json_decode($json, true);

        return new CustomerCreateOrUpdateEvent(
            $data['customer']['civilityTitle']['id'],
            $data['customer']['firstname'],
            $data['customer']['lastname'],
            $data['address']['address1'],
            isset($data['address']['address2']) ? $data['address']['address2'] : null,
            isset($data['address']['address3']) ? $data['address']['address3'] : null,
            isset($data['address']['phoneNumber']) ? $data['address']['phoneNumber'] : null,
            isset($data['address']['cellphoneNumber']) ? $data['address']['cellphoneNumber'] : null,
            $data['address']['zipCode'],
            $data['address']['city'],
            $data['address']['countryCode']->getId,
            $data['customer']['email'],
            $data['password'],
            $data['customer']['lang']['id'],
            isset($data['customer']['reseller']) ? $data['customer']['reseller'] : null,
            null,
            isset($data['customer']['discount']) ? $data['customer']['discount'] : 0,
            isset($data['address']['company']) ? $data['address']['company'] : null,
            null
        );
    }
}