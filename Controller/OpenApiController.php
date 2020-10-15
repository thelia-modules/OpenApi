<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\BaseController;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Model\CustomerQuery;
use function OpenApi\scan;

/**
 * @OA\Info(title="Thelia Open Api", version="0.1")
 * @OA\SecurityScheme(
 *          securityScheme="cookieAuth",
 *          type="apiKey",
 *          in="cookie",
 *          name="PHPSESSID"
 * )
 * @OA\OpenApi(
 *      security={
 *         {"cookieAuth": {}}
 *      }
 * )
 */
class OpenApiController extends BaseFrontController
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function getDocumentation()
    {
        header("Access-Control-Allow-Origin: *");

        $controllerApi = scan([__DIR__.'/../Controller', __DIR__.'/../Model']);

        $annotations = json_decode($controllerApi->toJson(), true);


        $annotations['servers'] = [
            ["url" => "http://localhost:8080/open_api"],
            ["url" => "http://localhost:8080/index_dev.php/open_api"]
        ];

        return new JsonResponse($annotations);

    }

    /**
     * @Route("/login", name="login", methods="GET")
     *
     * @OA\Get(
     *     path="/login",
     *     summary="Login",
     *     security={},
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="password",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *     )
     * )
     */
    public function login(Request $request)
    {
        header("Access-Control-Allow-Origin: *");

        $customer = CustomerQuery::create()->findOneByEmail($request->get('email'));

        $this->getDispatcher()->dispatch(
            TheliaEvents::CUSTOMER_LOGIN,
            new CustomerLoginEvent($customer)
        );

        return $this->render('swagger-ui', [
            'spec' => json_encode($annotations)
        ]);

    }
}