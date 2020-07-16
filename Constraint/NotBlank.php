<?php

namespace OpenApi\Constraint;

use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class NotBlank extends \Symfony\Component\Validator\Constraints\NotBlank
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->message = Translator::getInstance()->trans("This value should not be blank", [], OpenApi::DOMAIN_NAME);
    }

    public function validatedBy()
    {
        return \get_parent_class().'Validator';
    }
}