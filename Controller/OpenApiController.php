<?php

namespace OpenApi\Controller;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Util;
use Thelia\Core\HttpFoundation\JsonResponse;
use function OpenApi\scan;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Request;

/**
 * @OA\Info(title="Thelia Open Api", version="0.1")
 */
class OpenApiController extends BaseFrontController
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function getDocumentation(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $directories = [
            THELIA_MODULE_DIR.'/*/Model/Api',
            THELIA_MODULE_DIR.'/*/Model/OpenApi',
            THELIA_MODULE_DIR.'/*/EventListener',
            THELIA_MODULE_DIR.'/*/ApiExtend',
            THELIA_MODULE_DIR.'/*/Controller',
        ];

        $validDirectories = [];

        foreach ($directories as $directory) {
            $matches = glob($directory);

            if (!empty($matches)) {
                $validDirectories[] = $directory;
            }
        }

        $annotations = Generator::scan(Util::finder($validDirectories));

        $annotations = json_decode($annotations?->toJson(), true);

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

        $host = $request->getSchemeAndHttpHost();
        $annotations['servers'] = [
            ['url' => $host.'/open_api'],
            ['url' => $host.'/index_dev.php/open_api'],
        ];

        return $this->render('swagger-ui', [
            'spec' => json_encode($annotations),
        ]);
    }
}
