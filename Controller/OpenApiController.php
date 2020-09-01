<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Thelia\Controller\BaseController;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Model\CustomerQuery;
use function OpenApi\scan;

/**
 * @OA\Info(title="Thelia Open Api", version="0.1")
 */
class OpenApiController extends BaseFrontController
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function getDocumentation()
    {
        header("Access-Control-Allow-Origin: *");

        $annotations = scan([ __DIR__.'/../Model', THELIA_MODULE_DIR.'/*/Controller']);
        $annotations = json_decode($annotations->toJson(), true);

        $host = $this->getRequest()->getSchemeAndHttpHost();
        $annotations['servers'] = [
            ["url" => $host."/open_api"],
            ["url" => $host."/index_dev.php/open_api"]
        ];

        return new JsonResponse($annotations);

    }
}