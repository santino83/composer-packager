<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 13.54
 */

namespace Santino83\ComposerPackager\Utils;


use Composer\Config\JsonConfigSource;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;

class ComposerUtils
{

    /**
     * @param string $configPath
     * @param IOInterface|null $io
     * @param bool $authConfig
     * @return JsonConfigSource
     */
    public static function getConfig(string $configPath, IOInterface $io = null, bool $authConfig = false): JsonConfigSource
    {
        $realpath = realpath($configPath);
        $jsonFile = new JsonFile($realpath, null, $io);
        return new JsonConfigSource($jsonFile, $authConfig);
    }

}