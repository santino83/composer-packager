<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 06/07/18
 * Time: 16.25
 */

namespace Santino83\ComposerPackager\Utils;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigProcessor
 * @package Santino83\ComposerPackager\Utils
 */
class ConfigProcessor
{

    /**
     * Processes configuration like in Symfony Framework
     *
     * @param ConfigurationInterface $configuration the configuration schema
     * @param array $config the given configuration
     * @return array the processed configuration
     * @throws InvalidConfigurationException on configuration errors
     */
    public static function processConfiguration(ConfigurationInterface $configuration, array $config): array
    {
        return self::process($configuration->getConfigTreeBuilder(), $config);
    }

    /**
     * Processes configuration like in Symfony Framework
     *
     * @param TreeBuilder $treeBuilder the configuration schema builder
     * @param array $config the given configuration
     * @return array the processed configuration
     * @throws InvalidConfigurationException on configuration errors
     */
    public static function process(TreeBuilder $treeBuilder, array $config): array
    {
        $processor = new Processor();

        return $processor->process($treeBuilder->buildTree(), [$config]);
    }

}