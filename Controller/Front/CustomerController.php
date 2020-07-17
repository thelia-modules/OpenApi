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
            if (null === $currentCustomer = $this->getSecurityContext()->getCustomerUser()) {
                throw new \Exception(Translator::getInstance()->trans("No customer logged in.", [], OpenApi::DOMAIN_NAME));
            }

            return new JsonResponse((new \OpenApi\Model\Api\Customer())->createFromTheliaCustomer($currentCustomer), 200);
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
            $openApiCustomer = (new OpenApiCustomer())
                ->createFromData($data['customer'])
                ->validate(self::GROUP_CREATE)
            ;

            $openApiAddress = (new OpenApiAddress())
                ->createFromData($data['address'])
                ->validate(self::GROUP_CREATE)
            ;


            //todo
            $event = $this->createCustomerCreateEvent($request->getContent());

            $this->dispatch(TheliaEvents::CUSTOMER_CREATEACCOUNT, $event);

            $newCustomer = $event->getCustomer();
            return new JsonResponse((new \OpenApi\Model\Api\Customer())->createFromTheliaCustomer($newCustomer), 200);
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
            if (!$currentCustomer = $this->getSecurityContext()->getCustomerUser()) {
                throw new \Exception(Translator::getInstance()->trans('No customer currently logged in.', [], OpenApi::DOMAIN_NAME));
            }

            $data = json_decode($request->getContent(), true);
            $openApiCustomer = (new \OpenApi\Model\Api\Customer())
                ->createFromData($data['customer'])
                ->validate(self::GROUP_CREATE)
            ;


            //todo
            $event = $this->createCustomerUpdateEvent($request->getContent());
            $event->setCustomer($currentCustomer);

            $this->dispatch(TheliaEvents::CUSTOMER_UPDATEPROFILE, $event);

            return new JsonResponse((new \OpenApi\Model\Api\Customer())->createFromTheliaCustomer($event->getCustomer()), 200);
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