<?php

namespace OpenApi\Hook;

use OpenApi\Form\ConfigForm;
use OpenApi\OpenApi;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class BackHook extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $configVariables = explode(',', OpenApi::getConfigValue('config_variables'));
        $event->add($this->render('configuration.html', ['configs' => $configVariables]));
    }
}