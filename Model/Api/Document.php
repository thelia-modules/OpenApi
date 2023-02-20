<?php

namespace OpenApi\Model\Api;

use OpenApi\Service\DocumentService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\TaxEngine\TaxEngine;

class Document extends File
{
    static $serviceAliases = ["ProductDocuments"];
    
    /** @var DocumentService */
    protected $documentService;


    public function __construct(
        ModelFactory $modelFactory,
        RequestStack $requestStack,
        TaxEngine $taxEngine,
        EventDispatcherInterface $dispatcher,
        ValidatorInterface $validator,
        DocumentService $documentService
    )
    {
        parent::__construct($modelFactory, $requestStack, $taxEngine, $dispatcher, $validator);
        $this->documentService = $documentService;
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
        $this->url = $this->documentService->getDocumentUrl($theliaModel, $type);

        return $this;
    }
}
