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
                $modelServices[$attributes['alias']] = $id;
            }
        }

        $containerBuilder->setParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY, $modelServices);
    }
}