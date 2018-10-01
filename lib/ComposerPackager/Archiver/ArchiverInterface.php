<?php

/*
 *  Voiptech s.r.l. 2017-2020
 */

namespace Santino83\ComposerPackager\Archiver;

/**
 *
 * @author <a href="mailto:gsantini@voiptech.it">Giorgio M. Santini</a>
 */
interface ArchiverInterface
{
    
    function archive(array $config): ArchiveInterface;
    
}
