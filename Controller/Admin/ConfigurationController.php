<?php

namespace OpenApi\Controller\Admin;

use OpenApi\Form\ConfigForm;
use OpenApi\OpenApi;
use Thelia\Controller\Admin\BaseAdminController;
use Symfony\Component\Routing\Annotation\Route;
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
    public function saveAction(ParserContext $parserContext)
    {
        $configForm = $this->createForm(ConfigForm::getName());

        try {
            $form = $this->validateForm($configForm);

            $data = implode(',', array_keys($form->get('enable_config')->getData()));

            OpenApi::setConfigValue('config_variables', $data);

            return $this->generateSuccessRedirect($configForm);
        } catch (\Exception $exception) {
            Tlog::getInstance()->error($exception->getMessage());

            $configForm->setErrorMessage($exception->getMessage());

            $parserContext
                ->addForm($configForm)
                ->setGeneralError($exception->getMessage())
            ;

            return $this->generateErrorRedirect($configForm);
        }
    }
}