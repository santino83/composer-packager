<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 13.47
 */

namespace Santino83\ComposerPackager\Publish;


interface VersionResolverInterface
{

    /**
     * Resolves the current project version
     *
     * @param array $config the config
     * @return string the version
     */
    function resolve(array $config): string;

}