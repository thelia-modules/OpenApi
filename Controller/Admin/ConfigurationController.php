<?php

namespace OpenApi\Controller\Admin;

use OpenApi\Form\ConfigForm;
use OpenApi\OpenApi;
use Thelia\Controller\Admin\BaseAdminController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Thelia\Core\Template\ParserContext;
use Thelia\Log\Tlog;

/**
 * @Route("/admin/module/OpenApi", name="config_configuration")
 */
class ConfigurationController extends BaseAdminController
{
    /**
     * @Route("/save", name="_save", methods="POST")
     */
    public function saveAction()
    {
        $configForm = $this->createForm('open_api_config_form');

        try {
            $form = $this->validateForm($configForm);

            $data = implode(',', array_keys($form->get('enable_config')->getData()));

            OpenApi::setConfigValue('config_variables', $data);

            return $this->generateSuccessRedirect($configForm);
        } catch (\Exception $exception) {
            Tlog::getInstance()->error($exception->getMessage());

            $configForm->setErrorMessage($exception->getMessage());

            $this->getParserContext()
                ->addForm($configForm)
                ->setGeneralError($exception->getMessage())
            ;

            return $this->generateErrorRedirect($configForm);
        }
    }
}