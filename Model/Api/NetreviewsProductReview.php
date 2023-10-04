<?php

namespace OpenApi\Model\Api;

use DateTime;
use OpenApi\Annotations as OA;
use OpenApi\Constraint;

/**
 * Class NetreviewsProductReview.
 *
 * @OA\Schema(
 *     schema="NetreviewsProductReview",
 *     description="A Product Review"
 * )
 */
class NetreviewsProductReview extends BaseApiModel
{
    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $productReviewId;

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
    protected $email;

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
    protected $reviewDate;

    /**
     * @var string
     *
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $message;

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
    protected $productRef;

    /**
     * @var int
     *
     * @OA\Property(
     *     type="integer",
     * )
     */
    protected $productId;

    /**
     * @var int
     *
     * @OA\Property(
     *     type="interger",
     * )
     */
    protected $exchange;

    /**
     * @return string
     */
    public function getProductReviewId(): string
    {
        return $this->productReviewId;
    }

    /**
     * @param string $productReviewId
     * @return NetreviewsProductReview
     */
    public function setProductReviewId(string $productReviewId): NetreviewsProductReview
    {
        $this->productReviewId = $productReviewId;
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
     * @return NetreviewsProductReview
     */
    public function setReviewId(string $reviewId): NetreviewsProductReview
    {
        $this->reviewId = $reviewId;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return NetreviewsProductReview
     */
    public function setEmail(?string $email): NetreviewsProductReview
    {
        $this->email = $email;
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
     * @return NetreviewsProductReview
     */
    public function setLastName(?string $lastName): NetreviewsProductReview
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
     * @return NetreviewsProductReview
     */
    public function setFirstName(?string $firstName): NetreviewsProductReview
    {
        $this->firstName = $firstName;
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
     * @return NetreviewsProductReview
     */
    public function setReviewDate(?DateTime $reviewDate): NetreviewsProductReview
    {
        $this->reviewDate = $reviewDate->format('Y-m-d H:i:s');
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return NetreviewsProductReview
     */
    public function setMessage(?string $message): NetreviewsProductReview
    {
        $this->message = $message;
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
     * @return NetreviewsProductReview
     */
    public function setRate(?string $rate): NetreviewsProductReview
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
     * @return NetreviewsProductReview
     */
    public function setOrderRef(?string $orderRef): NetreviewsProductReview
    {
        $this->orderRef = $orderRef;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductRef(): string
    {
        return $this->productRef;
    }

    /**
     * @param string $productRef
     * @return NetreviewsProductReview
     */
    public function setProductRef(?string $productRef): NetreviewsProductReview
    {
        $this->productRef = $productRef;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     * @return NetreviewsProductReview
     */
    public function setProductId(?int $productId): NetreviewsProductReview
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return int
     */
    public function getExchange(): int
    {
        return $this->exchange;
    }

    /**
     * @param int $exchange
     * @return NetreviewsProductReview
     */
    public function setExchange(?int $exchange): NetreviewsProductReview
    {
        $this->exchange = $exchange;
        return $this;
    }
}