<?php

namespace OpenApi\Controller\Admin;

use Thelia\Controller\Admin\BaseAdminController;

abstract class BaseAdminOpenApiController extends BaseAdminController
{
    const GROUP_CREATE = 'create';

    const GROUP_READ = 'read';

    const GROUP_UPDATE = 'update';

    const GROUP_DELETE = 'delete';
}
