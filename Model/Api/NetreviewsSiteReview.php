<?php

namespace OpenApi\Model\Api;

use DateTime;
use OpenApi\Annotations as OA;
use OpenApi\Constraint;

/**
 * Class NetreviewsSiteReview.
 *
 * @OA\Schema(
 *     schema="NetreviewsSiteReview",
 *     description="A Site Review"
 * )
 */
class NetreviewsSiteReview extends BaseApiModel
{
    /**
     * @var int
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $siteReviewId;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $reviewId;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $lastName;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $firstName;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $review;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $reviewDate;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $rate;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $orderRef;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $orderDate;

    /**
     * @return int
     */
    public function getSiteReviewId(): int
    {
        return $this->siteReviewId;
    }

    /**
     * @param int $siteReviewId
     * @return NetreviewsSiteReview
     */
    public function setSiteReviewId(int $siteReviewId): NetreviewsSiteReview
    {
        $this->siteReviewId = $siteReviewId;
        return $this;
    }

    /**
     * @return string
     */
    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    /**
     * @param string $reviewId
     * @return NetreviewsSiteReview
     */
    public function setReviewId(string $reviewId): NetreviewsSiteReview
    {
        $this->reviewId = $reviewId;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return NetreviewsSiteReview
     */
    public function setLastName(?string $lastName): NetreviewsSiteReview
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return NetreviewsSiteReview
     */
    public function setFirstName(?string $firstName): NetreviewsSiteReview
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getReview(): string
    {
        return $this->review;
    }

    /**
     * @param string $review
     * @return NetreviewsSiteReview
     */
    public function setReview(?string $review): NetreviewsSiteReview
    {
        $this->review = $review;
        return $this;
    }

    /**
     * @return string
     */
    public function getReviewDate(): string
    {
        return $this->reviewDate;
    }

    /**
     * @param DateTime $reviewDate
     * @return NetreviewsSiteReview
     */
    public function setReviewDate(?DateTime $reviewDate): NetreviewsSiteReview
    {
        $this->reviewDate = $reviewDate->format('Y-m-d H:i:s');
        return $this;
    }

    /**
     * @return string
     */
    public function getRate(): string
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     * @return NetreviewsSiteReview
     */
    public function setRate(?string $rate): NetreviewsSiteReview
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderRef(): string
    {
        return $this->orderRef;
    }

    /**
     * @param string $orderRef
     * @return NetreviewsSiteReview
     */
    public function setOrderRef(?string $orderRef): NetreviewsSiteReview
    {
        $this->orderRef = $orderRef;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    /**
     * @param DateTime $orderDate
     * @return NetreviewsSiteReview
     */
    public function setOrderDate(?DateTime $orderDate): NetreviewsSiteReview
    {
        $this->orderDate = $orderDate->format('Y-m-d H:i:s');
        return $this;
    }


}