<?php

namespace BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class IncompleteProfileCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('base.incomplete_profile.service.provider')) {
            return;
        }

        $definition = $container->getDefinition('base.incomplete_profile.service.provider');
        $taggedServices = $container->findTaggedServiceIds('base.incomplete_profile');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addIncompleteProfileService', [
                    new Reference($id),
                ]);
            }
        }
    }
}
