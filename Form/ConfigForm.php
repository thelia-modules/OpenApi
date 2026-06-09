<?php

namespace OpenApi\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Thelia\Form\BaseForm;

class ConfigForm extends BaseForm
{
    public static function getName(): string
    {
        return 'openapi_config_form';
    }

    protected function buildForm(): void
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
            ->add('success_url', HiddenType::class, ['required' => false])
            ->add('error_url', HiddenType::class, ['required' => false])
        ;
    }
}
