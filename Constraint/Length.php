<?php

namespace OpenApi\Constraint;

use OpenApi\OpenApi;
use Thelia\Core\Translation\Translator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Length extends \Symfony\Component\Validator\Constraints\Length
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $translator = Translator::getInstance();

        $this->maxMessage = $translator->trans('This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.', [], OpenApi::DOMAIN_NAME);
        $this->minMessage = $translator->trans('This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.', [], OpenApi::DOMAIN_NAME);
        $this->exactMessage = $translator->trans('This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.', [], OpenApi::DOMAIN_NAME);
        $this->charsetMessage = $translator->trans('This value does not match the expected {{ charset }} charset.', [], OpenApi::DOMAIN_NAME);
    }

    public function validatedBy()
    {
        return get_parent_class().'Validator';
    }
}
