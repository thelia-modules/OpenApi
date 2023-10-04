<?php

namespace OpenApi\Controller\Front;

use NetReviews\Model\NetreviewsProductReviewQuery;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\HttpFoundation\JsonResponse;

/**
 * @Route("/netreviews_product_review", name="netreviews_product_review")
 */
class NetreviewsProductReview  extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="get_product_review", methods="GET")
     *
     * @OA\Get(
     *     path="/netreviews_product_review",
     *     tags={"netreviews_product_review"},
     *     summary="Get netreviews_product_review",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/NetreviewsProductReview")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getProductReview(ModelFactory $modelFactory): JsonResponse
    {
        $productReviews = NetreviewsProductReviewQuery::create()->find();

        return OpenApiService::jsonResponse(
            array_map(fn($productReviews) => $modelFactory->buildModel('NetreviewsProductReview', $productReviews), iterator_to_array($productReviews))
        );
    }

}