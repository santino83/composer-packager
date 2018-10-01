<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 30/09/18
 * Time: 22.27
 */

use Santino83\ComposerPackager\Packager;

require_once __DIR__.'/../../vendor/autoload.php';

$application = new Packager();
$application->run();