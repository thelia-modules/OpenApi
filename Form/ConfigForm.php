<?php

namespace OpenApi\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ConfigForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'enable_config',
                CollectionType::class,
                [
                    'entry_type' => CheckboxType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ;
    }

}