<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 27/09/18
 * Time: 17.40
 */

namespace Santino83\ComposerPackager\Repository;

use Santino83\ComposerPackager\Exception\RepositoryConfigException;

/**
 * Interface RepositoryConfigValidatorInterface
 * @package Santino83\ComposerPackager\Repository
 */
interface RepositoryConfigValidatorInterface
{

    /**
     * Validates the given remote repository configuration
     *
     * @param array $repositoryConfig the configuration to be checked
     * @return bool true/false
     * @throws RepositoryConfigException on configuration error
     */
    function validate(array $repositoryConfig): bool;

}