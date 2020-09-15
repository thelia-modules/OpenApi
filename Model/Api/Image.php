<?php


namespace OpenApi\Model\Api;

use OpenApi\Service\ImageService;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\TaxEngine\TaxEngine;

class Image extends File
{
    /** @var ImageService */
    protected $imageService;

    public function __construct(ModelFactory $modelFactory, RequestStack $requestStack, TaxEngine $taxEngine, EventDispatcher $dispatcher, ImageService $imageService)
    {
        parent::__construct($modelFactory, $requestStack, $taxEngine, $dispatcher);
        $this->imageService = $imageService;
    }

    /**
     * @param $theliaModel
     * @param null $locale
     * @param null $type
     * @return $this
     */
    public function createFromTheliaModel($theliaModel, $locale = null, $type = null)
    {
        parent::createFromTheliaModel($theliaModel, $locale);
        $this->url = $this->imageService->getImageUrl($theliaModel, $type);

        return $this;
    }
}
