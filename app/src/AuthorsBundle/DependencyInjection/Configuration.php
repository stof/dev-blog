<?php

namespace Symbid\DevBlog\AuthorsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $rootNode = $treeBuilder->root('symbid_authors');

        $rootNode
            ->children()
                ->arrayNode('authors')
                    ->useAttributeAsKey('alias', false)
                    ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('bio')->isRequired()->end()
                        ->scalarNode('email')->end()
                        ->scalarNode('avatar')->end()
                        ->scalarNode('url')->end()
                        ->scalarNode('twitter')->end()
                        ->scalarNode('twitter_handle')->end()
                        ->scalarNode('github')->end()
                    ->end()
                ->end()
            ->end()
        ->end();


        return $treeBuilder;
    }
}
