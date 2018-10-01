<?php

/*
 *  Voiptech s.r.l. 2017-2020
 */

namespace Santino83\ComposerPackager\Archiver;

/**
 * Interface ArchiveInterface
 * @package Santino83\ComposerPackager\Archiver
 */
interface ArchiveInterface
{

    /**
     * Returns the name of the archive without extension
     *
     * @return string
     */
    function getName(): string;

    /**
     * Returns the type of the archive (e.g. the extension)
     *
     * @return string
     */
    function getType(): string;

    /**
     * Returns the full path of the archive, including file name and extension
     *
     * @return string
     */
    function getPath(): string;
    
}
