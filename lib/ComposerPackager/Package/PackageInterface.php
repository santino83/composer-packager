<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 06/07/18
 * Time: 17.38
 */

namespace Santino83\ComposerPackager\Package;

use Santino83\ComposerPackager\Archiver\ArchiveInterface;

/**
 * Interface PackageInterface
 * @package Santino83\ComposerPackager\Package
 */
interface PackageInterface
{

    /**
     * Returns the package name, eg: MyCompany/MyPackage
     *
     * @return string the package name
     */
    function getName(): string;

    /**
     * Returns the package version
     *
     * @return string the version
     */
    function getVersion(): string;

    /**
     * Gets if this is a development version or not
     *
     * @return bool true/false
     */
    function isDev(): bool;

    /**
     * Returns the package archive
     *
     * @return ArchiveInterface the archive
     */
    function getArchive(): ArchiveInterface;

}