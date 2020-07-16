<?php

namespace OpenApi\Constraint;

use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class NotNull extends \Symfony\Component\Validator\Constraints\NotNull
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->message = Translator::getInstance()->trans("This value should not be null", [], OpenApi::DOMAIN_NAME);
    }

    public function validatedBy()
    {
        return \get_parent_class().'Validator';
    }
}