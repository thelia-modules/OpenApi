<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
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

        $annotations = scan([
            THELIA_MODULE_DIR.'/*/Model/Api',
            THELIA_MODULE_DIR.'/*/EventListener',
            THELIA_MODULE_DIR.'/*/Controller'
        ]);
        $annotations = json_decode($annotations->toJson(), true);

        $modelAnnotations = $annotations['components']['schemas'];
        foreach ($modelAnnotations as $modelName => $modelAnnotation) {
            $isExtend = preg_match('/.*(Extend)(.*)/', $modelName, $matches);
            if (!$isExtend) {
                continue;
            }

            $modelExtendedName = $matches[2];

            $modelAnnotations[$modelExtendedName] = array_replace_recursive($modelAnnotations[$modelExtendedName], $modelAnnotation);
            unset($modelAnnotations[$modelName]);
        }

        $annotations['components']['schemas'] = $modelAnnotations;

        $host = $this->getRequest()->getSchemeAndHttpHost();
        $annotations['servers'] = [
            ["url" => $host."/open_api"],
            ["url" => $host."/index_dev.php/open_api"]
        ];

        return new JsonResponse($annotations);

    }
}