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
use OpenApi\Model\Api\Customer as OpenApiCustomer;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\OpenApi;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\SecurityContext;
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
     *     security={},
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
    public function customerLogin(
        Request $request,
        SecurityContext $securityContext,
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory
    ) {
        if ($securityContext->hasCustomerUser()) {
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

        $dispatcher->dispatch(new CustomerLoginEvent($customer), TheliaEvents::CUSTOMER_LOGIN);

        /* If the rememberMe property is set to true, we create a new cookie to store the information */
        if (true === (bool) $data['rememberMe']) {
            (new CookieTokenProvider())->createCookie(
                $customer,
                ConfigQuery::read('customer_remember_me_cookie_name', 'crmcn'),
                ConfigQuery::read('customer_remember_me_cookie_expiration', 2592000 /* 1 month */)
            );
        }

        /** @var OpenApiCustomer $openApiCustomer */
        $openApiCustomer = $modelFactory->buildModel('Customer', $customer);
        $openApiCustomer->setDefaultAddressId($customer->getDefaultAddress()->getId());

        return OpenApiService::jsonResponse($openApiCustomer);
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
    public function customerLogout(
        SecurityContext $securityContext,
        EventDispatcherInterface $dispatcher
    ) {
        if (!$securityContext->hasCustomerUser()) {
            throw new \Exception(Translator::getInstance()->trans('No user is currently logged in.'));
        }

        $dispatcher->dispatch((new BaseAction()), TheliaEvents::CUSTOMER_LOGOUT);
        (new CookieTokenProvider())->clearCookie(ConfigQuery::read('customer_remember_me_cookie_name', 'crmcn'));

        return OpenApiService::jsonResponse('Success', 204);
    }
}
