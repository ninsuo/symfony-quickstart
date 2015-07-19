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
                        ->scalarNode("check_url")
                            ->defaultValue('https://www.google.com/recaptcha/api/siteverify')
                        ->end()
                        ->scalarNode("post_param")
                            ->defaultValue('g-recaptcha-response')
                        ->end()
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
