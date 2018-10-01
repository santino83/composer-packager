<?php

/*
 *  Voiptech s.r.l. 2017-2020
 */

namespace Santino83\ComposerPackager\Repository;

use Santino83\ComposerPackager\Exception\RepositoryConfigException;
use Santino83\ComposerPackager\Package\PackageInterface;

/**
 * Interface RepositoryInterface
 * @package Santino83\ComposerPackager\Repository
 */
interface RepositoryInterface
{

    /**
     * Publishes the given package to the remote repository using the provided configuration
     *
     * @param PackageInterface $package the package to be published
     * @param array $repositoryConfig the remote repository configuration
     * @return bool true/false
     * @throws RepositoryConfigException on configuration or publish error
     */
    function publish(PackageInterface $package, array $repositoryConfig): bool;

    /**
     * Checks if this repository supports the given repository type
     *
     * @param string $type the type
     * @return bool true/false
     */
    function support(string $type): bool;

}
