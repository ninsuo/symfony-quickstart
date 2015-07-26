<?php

namespace Fuz\QuickStartBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('fuz_quick_start');

        $rootNode
           ->children()
                ->arrayNode('no_captcha')
                    ->isRequired()
                    ->children()
                        ->scalarNode("site_key")
                            ->isRequired()
                        ->end()
                        ->scalarNode("secret_key")
                            ->isRequired()
                        ->end()
                        ->arrayNode('sessions_per_ip')
                            ->isRequired()
                            ->children()
                                ->integerNode('max')
                                    ->defaultValue(20)
                                ->end()
                                ->integerNode('delay')
                                    ->defaultValue(3600)
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('strategies')
                            ->defaultValue(array())
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->integerNode('hits')
                                        ->isRequired()
                                    ->end()
                                    ->integerNode('delay')
                                        ->isRequired()
                                    ->end()
                                    ->booleanNode('reset')
                                        ->isRequired()
                                    ->end()
                                    ->arrayNode('methods')
                                        ->beforeNormalization()
                                            ->ifTrue(function($v) { return $v === null; })
                                            ->then(function($v) { return array(); })
                                        ->end()
                                        ->prototype('scalar')->end()
                                        ->defaultValue(array())
                                        ->validate()
                                            ->ifTrue(function($methods) {
                                                $allowed = array('HEAD', 'GET', 'POST', 'PUT', 'DELETE');
                                                $diff = array_diff(array_map('strtoupper', $methods), $allowed);
                                                return count($diff);
                                            })
                                            ->thenInvalid("Captcha configuration: unknown HTTP method: %s")
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                       ->end()
                   ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
