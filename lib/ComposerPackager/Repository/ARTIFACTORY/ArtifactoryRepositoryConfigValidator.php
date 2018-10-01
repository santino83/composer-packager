<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 27/09/18
 * Time: 17.45
 */

namespace Santino83\ComposerPackager\Repository\ARTIFACTORY;


use Santino83\ComposerPackager\Repository\AbstractRepositoryConfigValidator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class ArtifactoryRepositoryConfigValidator
 * @package Santino83\ComposerPackager\Repository\ARTIFACTORY
 */
class ArtifactoryRepositoryConfigValidator extends AbstractRepositoryConfigValidator
{
    /**
     * @inheritDoc
     */
    protected function getConfigurationTree(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ARTIFACTORY');

        $rootNode->children()
            ->scalarNode('endpoint')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('username')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }

}