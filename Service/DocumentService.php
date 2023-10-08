<?php


namespace OpenApi\Service;


use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Document\DocumentEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ConfigQuery;

class DocumentService
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns an document URL.
     *
     * @param $documentModel
     * @param $documentType
     *
     * @return string
     */
    public function getDocumentUrl($documentModel, $documentType = null)
    {
        if (null === $documentType) {
            $documentType = str_replace(['document', 'thelia\\model\\'], '', strtolower(\get_class($documentModel)));
        }

        $baseSourceFilePath = ConfigQuery::read('documents_library_path');
        $baseSourceFilePath = 
            $baseSourceFilePath === null ?THELIA_LOCAL_DIR.'media'.DS.'documents' : THELIA_ROOT.$baseSourceFilePath;

        $event = new DocumentEvent();
        // Put source document file path
        $sourceFilePath = sprintf(
            '%s/%s/%s',
            $baseSourceFilePath,
            $documentType,
            $documentModel->getFile()
        );

        $event->setSourceFilepath($sourceFilePath);
        $event->setCacheSubdirectory($documentType);

        $this->dispatcher->dispatch($event, TheliaEvents::DOCUMENT_PROCESS);

        return $event->getDocumentUrl();
    }
}
