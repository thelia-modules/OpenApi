<?php

/*      This file is part of the Thelia package.                                     */

/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */

/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */

namespace OpenApi;

use OpenApi\Compiler\ModelPass;
use OpenApi\Model\Api\BaseApiModel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Module\BaseModule;

class OpenApi extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'openapi';

    const PICKUP_ADDRESS_SESSION_KEY = 'pickup_address';

    const OPEN_API_ROUTE_REQUEST_KEY = 'is_open_api_route';

    const OPEN_API_MODELS_PARAMETER_KEY = 'OPEN_API_MODELS';

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    public static function getCompilers()
    {
        return [
            new ModelPass(),
        ];
    }

    /**
     * Defines how services are loaded in your modules.
     */
    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR.ucfirst(self::getModuleCode()).'/I18n/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }

    public static function loadConfiguration(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->registerForAutoconfiguration(BaseApiModel::class)
            ->addTag('open_api.model');
    }

    public static function getAnnotationRoutePrefix(): string
    {
        return 'open_api';
    }
}
