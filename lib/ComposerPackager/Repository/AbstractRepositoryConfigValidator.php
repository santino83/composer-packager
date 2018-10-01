<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 27/09/18
 * Time: 17.42
 */

namespace Santino83\ComposerPackager\Repository;


use Santino83\ComposerPackager\Exception\RepositoryConfigException;
use Santino83\ComposerPackager\Utils\ConfigProcessor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class AbstractRepositoryConfigValidator
 * @package Santino83\ComposerPackager\Repository
 */
abstract class AbstractRepositoryConfigValidator implements RepositoryConfigValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(array $repositoryConfig): bool
    {
        try {
            return is_array(ConfigProcessor::process($this->getConfigurationTree(), $repositoryConfig));
        } catch (InvalidConfigurationException $ex) {
            throw new RepositoryConfigException($ex->getMessage(), 500, $ex);
        }
    }

    /**
     * Returns the TreeBuilder to be used when validating repository configuration
     *
     * @return TreeBuilder the TreeBuilder to be used
     */
    abstract protected function getConfigurationTree(): TreeBuilder;

}