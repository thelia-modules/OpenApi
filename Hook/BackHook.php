<?php

namespace OpenApi\Hook;

use OpenApi\Form\ConfigForm;
use OpenApi\OpenApi;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\Base\ConfigQuery;

class BackHook extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $configVariables = explode(',', OpenApi::getConfigValue('config_variables'));
        $theliaConfigs = ConfigQuery::create()->find()->toArray();
        $event->add($this->render('configuration.html', ['configs' => $configVariables, 'theliaConfigs' => $theliaConfigs]));
    }
}