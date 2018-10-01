<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Santino83\\', __DIR__);

date_default_timezone_set('UTC');


define("ASSETS_DIR", __DIR__.'/assets');

// autoload doctrine annotations
/*use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
*/

include 'env.php';