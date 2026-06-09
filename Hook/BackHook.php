<?php

namespace OpenApi\Hook;

use OpenApi\Form\ConfigForm;
use OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\Required;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ConfigQuery;

class BackHook extends BaseHook
{
    #[Required]
    public ?TheliaFormFactory $formFactory = null;

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $enabledConfigs = array_filter(
            array_map('intval', explode(',', (string) OpenApi::getConfigValue('config_variables', '')))
        );

        $form = $this->formFactory->createForm(ConfigForm::getName());
        $form->createView();

        $allConfigs = ConfigQuery::create()->orderByName()->find();
        $configs = [];
        foreach ($allConfigs as $config) {
            $configs[] = ['id' => $config->getId(), 'name' => $config->getName()];
        }

        $event->add($this->render('configuration.html.twig', [
            'form' => $form->getView(),
            'configs' => $configs,
            'enabled_configs' => $enabledConfigs,
        ]));
    }
}
