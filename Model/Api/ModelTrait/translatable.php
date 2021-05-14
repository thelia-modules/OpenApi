<?php

namespace OpenApi\Model\Api\ModelTrait;

use OpenApi\Model\Api\I18n;
use OpenApi\Model\Api\ModelFactory;

/**
 * @OA\Schema
 * Trait translatable
 */
trait translatable
{
    /**
     * @var I18n
     * @OA\Property(
     *     ref="#/components/schemas/I18n"
     * )
     */
    protected $i18n;

    /**
     * @param ModelFactory $modelFactory
     *
     * used to build an empty i18n model at model initialisation
     */
    public function initI18n(ModelFactory $modelFactory): void
    {
        $this->i18n = $modelFactory->buildModel('I18n');
    }

    public function getI18n()
    {
        return $this->i18n;
    }

    /**
     * @param string $title
     *
     * @return translatable
     */
    public function setTitle($title)
    {
        $this->i18n->setTitle($title);

        return $this;
    }

    /**
     * @param string $description
     *
     * @return translatable
     */
    public function setDescription($description)
    {
        $this->i18n->setDescription($description);

        return $this;
    }

    /**
     * @param string $chapo
     *
     * @return translatable
     */
    public function setChapo($chapo)
    {
        $this->i18n->setChapo($chapo);

        return $this;
    }

    /**
     * @param string $postscriptum
     *
     * @return translatable
     */
    public function setPostscriptum($postscriptum)
    {
        $this->i18n->setPostscriptum($postscriptum);

        return $this;
    }

    /**
     * @param string $metaTitle
     *
     * @return translatable
     */
    public function setMetaTitle($metaTitle)
    {
        $this->i18n->setMetaTitle($metaTitle);

        return $this;
    }

    /**
     * @param string $metaDescription
     *
     * @return translatable
     */
    public function setMetaDescription($metaDescription)
    {
        $this->i18n->setMetaDescription($metaDescription);

        return $this;
    }

    /**
     * @param string $metaKeywords
     *
     * @return translatable
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->i18n->setMetaKeywords($metaKeywords);

        return $this;
    }
}
