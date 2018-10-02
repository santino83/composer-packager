<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 02/10/18
 * Time: 11.51
 */

namespace Santino83\ComposerPackager\Publish;


interface PackageNameResolverInterface
{

    /**
     * Resolves the current project name
     *
     * @param array $config the config
     * @return name the name
     */
    function resolve(array $config): string;

}