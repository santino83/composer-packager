<?php

/*
 *  Voiptech s.r.l. 2017-2020
 */

namespace Santino83\ComposerPackager\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Santino83\ComposerPackager\Config
 * @author <a href="mailto:gsantini@voiptech.it">Giorgio M. Santini</a>
 */
class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('packager');

        $rootNode->children()
                    ->scalarNode('composer')->defaultValue('./composer.json')->end()
                    ->scalarNode('name')->defaultNull()->end() //override name in composer.json
                    ->scalarNode('version')->defaultNull()->end() //override version in composer.json
                    ->enumNode('archive-format')->defaultValue('zip')->values(['zip','tar'])->cannotBeEmpty()->end()
                ->end()
                ->fixXmlConfig('repository')
                ->children()
                    ->arrayNode('repositories')
                    ->cannotBeEmpty()
                    ->useAttributeAsKey('type')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end();


        return $treeBuilder;
    }

}
