<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace OpenApi;

use OpenApi\Compiler\ModelPass;
use Thelia\Module\BaseModule;

class OpenApi extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'openapi';

    const PICKUP_ADDRESS_SESSION_KEY = "pickup_address";

    const OPEN_API_ROUTE_REQUEST_KEY = "is_open_api_route";

    const OPEN_API_MODELS_PARAMETER_KEY = "OPEN_API_MODELS";


    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    public static function getCompilers()
    {
        return [
            new ModelPass()
        ];
    }
}
