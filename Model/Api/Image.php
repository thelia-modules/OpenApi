<?php

namespace OpenApi\Model\Api;

use OpenApi\Service\ImageService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\TaxEngine\TaxEngine;

class Image extends File
{
    /** @var ImageService */
    protected $imageService;


    public function __construct(
        ModelFactory $modelFactory,
        RequestStack $requestStack,
        TaxEngine $taxEngine,
        EventDispatcherInterface $dispatcher,
        ValidatorInterface $validator,
        ImageService $imageService
    )
    {
        parent::__construct($modelFactory, $requestStack, $taxEngine, $dispatcher, $validator);
        $this->imageService = $imageService;
    }

    /**
     * @param $theliaModel
     * @param null $locale
     * @param null $type
     *
     * @return $this
     */
    public function createFromTheliaModel($theliaModel, $locale = null, $type = null)
    {
        parent::createFromTheliaModel($theliaModel, $locale);
        $this->url = $this->imageService->getImageUrl($theliaModel, $type);

        return $this;
    }

    public function fromTheliaImageLight($theliaImage, $locale = null): self
    {
        $this->id = $theliaImage->getId();
        if (method_exists($theliaImage, 'getPosition')) {
            $this->position = $theliaImage->getPosition();
        }
        if (method_exists($theliaImage, 'getVisible')) {
            $this->visible = $theliaImage->getVisible();
        }
        if (method_exists($theliaImage, 'getTranslation')) {
            $translation = $theliaImage->getTranslation($locale ?? $this->getCurrentLocale());
            if (null !== $translation) {
                $this->setTitle($translation->getTitle() ?? '');
                $this->setChapo($translation->getChapo() ?? '');
                $this->setDescription($translation->getDescription() ?? '');
                $this->setPostscriptum($translation->getPostscriptum() ?? '');
            }
        }

        return $this;
    }
}
