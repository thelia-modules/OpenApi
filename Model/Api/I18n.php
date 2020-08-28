<?php

namespace OpenApi\Model\Api;

/**
 * Class I18n
 * @package OpenApi\Model\Api
 * @OA\Schema(
 *     description="Translatable fields for multiple object"
 * )
 */
class I18n extends BaseApiModel
{
    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $description;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $chapo;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $postscriptum;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $metaTitle;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $metaDescription;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $metaKeywords;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return I18n
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return I18n
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @param string $chapo
     *
     * @return I18n
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    /**
     * @param string $postscriptum
     *
     * @return I18n
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     *
     * @return I18n
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     *
     * @return I18n
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     *
     * @return I18n
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }
}