<?php

namespace OpenApi\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Zipcode extends Constraint
{
    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}
