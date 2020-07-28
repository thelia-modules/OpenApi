<?php


namespace OpenApi\Controller\Front;

use OpenApi\Model\Api\Customer;
use OpenApi\Model\Api\Error;
use OpenApi\OpenApi;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\Token\CookieTokenProvider;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ConfigQuery;
use Thelia\Model\CustomerQuery;

/**
 * @Route("", name="auth")
 */
class AuthController extends BaseFrontOpenApiController
{
    /**
     * @Route("/login", name="login", methods="POST")
     *
     * @OA\Post(
     *     path="/login",
     *     tags={"customer"},
     *     summary="Log in a customer",
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="rememberMe",
     *                     type="boolean"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function customerLogin(Request $request)
    {
        if ($this->getSecurityContext()->hasCustomerUser()) {
            throw new \Exception(Translator::getInstance()->trans('A user is already connected. Please disconnect before trying to login in another account.'));
        }

        $data = json_decode($request->getContent(), true);

        $customer = CustomerQuery::create()
            ->filterByEmail($data['email'])
            ->findOne()
        ;

        if (null === $customer) {
            throw new \Exception(Translator::getInstance()->trans('No customer found for this email.', [], OpenApi::DOMAIN_NAME));
        }

        if (!$customer->checkPassword($data['password'])) {
            throw new \Exception(Translator::getInstance()->trans('Password incorrect.', [], OpenApi::DOMAIN_NAME));
        }

        $this->dispatch(TheliaEvents::CUSTOMER_LOGIN, new CustomerLoginEvent($customer));

        /** If the rememberMe property is set to true, we create a new cookie to store the information */
        if (true === (bool)$data['rememberMe']) {
            (new CookieTokenProvider())->createCookie(
                $customer,
                ConfigQuery::read('customer_remember_me_cookie_name', 'crmcn'),
                ConfigQuery::read('customer_remember_me_cookie_expiration', 2592000 /* 1 month */)
            );
        }

        return $this->jsonResponse($this->getModelFactory()->buildModel('Customer', $customer));
    }

    /**
     * @Route("/logout", name="logout", methods="POST")
     *
     * @OA\Post(
     *     path="/logout",
     *     tags={"customer"},
     *     summary="Log out a customer",
     *
     *     @OA\Response(
     *          response="204",
     *          description="Success",
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function customerLogout(Request $request)
    {
        if (!$this->getSecurityContext()->hasCustomerUser()) {
            throw new \Exception(Translator::getInstance()->trans('No user is currently logged in.'));
        }

        $this->dispatch(TheliaEvents::CUSTOMER_LOGOUT);
        (new CookieTokenProvider())->clearCookie(ConfigQuery::read('customer_remember_me_cookie_name', 'crmcn'));

        return $this->jsonResponse("Success");
    }
}