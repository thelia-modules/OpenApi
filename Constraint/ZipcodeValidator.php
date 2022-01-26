<?php

namespace OpenApi\Constraint;

use OpenApi\OpenApi;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CountryQuery;

class ZipcodeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $address = $this->context->getRoot();
        $country = CountryQuery::create()->findPk($address->getCountryId());

        if (null !== $country) {
            if ($country->getNeedZipCode()) {
                $zipCodeRegExp = $country->getZipCodeRE();
                if (null !== $zipCodeRegExp) {
                    if (!preg_match($zipCodeRegExp, $value)) {
                        $this->context
                        ->buildViolation(Translator::getInstance()->trans(
                            "This zip code should respect the following format : %format.",
                            ['%format' => $country->getZipCodeFormat()],
                            OpenApi::DOMAIN_NAME
                        ))
                        ->addViolation();
                    }
                }
            }
        }

        return;
    }
}
