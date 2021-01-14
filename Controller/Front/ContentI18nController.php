<?php

namespace OpenApi\Controller\Front;

use Exception;
use OpenApi\OpenApi;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ContentI18nQuery;

/**
 * @Route("/contenti18n", name="content")
 */
class ContentI18nController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="get_contenti18n", methods="GET")
     *
     * @OA\Get(
     *     path="/contenti18n",
     *     tags={"contenti18n"},
     *     summary="Get content values by ID and lang",
     *     @OA\Parameter(
     *          name="id",
     *          in="query",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="lang",
     *          in="query",
     *          example="fr_FR",
     *          description="If is empty get Thelia locale value",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/I18n")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     * @throws Exception
     */
    public function getContentI18n(Request $request)
    {
        $id     = $request->get('id');
        $lang   = $request->get('lang');
        if(null === $lang)
            $lang   = $request->getSession()->getLang(true)->getLocale();

        $contentI18n = ContentI18nQuery::create()
            ->where("locale = '$lang'")
            ->findOneById($id);

        if (null === $contentI18n) {
            throw new Exception(Translator::getInstance()->trans("Content does not exist.", [], OpenApi::DOMAIN_NAME));
        }

        return new JsonResponse($contentI18n->toArray());
    }
}
