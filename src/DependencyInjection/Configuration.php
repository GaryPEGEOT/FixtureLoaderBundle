<?php

namespace MiaouCorp\Bundle\FixtureLoaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('miaoucorp_fixture_loader');
        $rootNode = \method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('miaoucorp_fixture_loader');
        $rootNode->children()
            ->scalarNode('directory')
                ->cannotBeEmpty()
                ->defaultValue('%kernel.project_dir%/tests/Resources/fixtures')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
