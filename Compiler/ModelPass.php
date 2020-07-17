<?php

namespace OpenApi\Compiler;

use OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        $taggedServices = $containerBuilder->findTaggedServiceIds('open_api.model');

        $modelServices = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $modelsServices[$attributes['alias']] = $id;
            }
        }

        define(OpenApi::OPEN_API_MODELS_CONSTANT_KEY, $modelServices);
    }
}