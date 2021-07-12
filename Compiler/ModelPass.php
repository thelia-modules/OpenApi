<?php

namespace OpenApi\Compiler;

use OpenApi\Model\Api\Address;
use OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $taggedServices = $containerBuilder->findTaggedServiceIds('open_api.model');

        $modelServices = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $classParts = explode('\\', $id);
                $modelAlias = $attributes['alias'] ?? end($classParts);
                $modelServices[$modelAlias] = $id;
            }
            if (property_exists($id, "serviceAliases")) {
                foreach ($id::$serviceAliases as $alias) {
                    $modelServices[$alias] = $id;
                }
            }
        }

        $containerBuilder->setParameter(OpenApi::OPEN_API_MODELS_PARAMETER_KEY, $modelServices);
    }
}
