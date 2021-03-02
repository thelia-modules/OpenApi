<?php

namespace OpenApi\Controller\Front;

use Thelia\Controller\Front\BaseFrontController;

abstract class BaseFrontOpenApiController extends BaseFrontController
{
    const GROUP_CREATE = 'create';

    const GROUP_READ = 'read';

    const GROUP_UPDATE = 'update';

    const GROUP_DELETE = 'delete';
}
