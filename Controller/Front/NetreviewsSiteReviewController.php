<?php

namespace OpenApi\Controller\Front;

use NetReviews\Model\NetreviewsSiteReviewQuery;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\JsonResponse;

/**
 * @Route("/netreviews_site_review", name="netreviews_site_review")
 */
class NetreviewsSiteReviewController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="get_site_review", methods="GET")
     *
     * @OA\Get(
     *     path="/netreviews_site_review",
     *     tags={"netreviews_site_review"},
     *     summary="Get netreviews_site_review",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/NetreviewsSiteReview")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getSiteReview(ModelFactory $modelFactory): JsonResponse
    {
        $siteReviews = NetreviewsSiteReviewQuery::create()->find();

        return OpenApiService::jsonResponse(
            array_map(fn($siteReviews) => $modelFactory->buildModel('NetreviewsSiteReview', $siteReviews), iterator_to_array($siteReviews))
        );
    }
}