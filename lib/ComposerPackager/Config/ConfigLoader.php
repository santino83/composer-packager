<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 0.22
 */

namespace Santino83\ComposerPackager\Config;


use Composer\IO\IOInterface;
use Santino83\ComposerPackager\Utils\ConfigProcessor;
use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{

    /**
     * @var string
     */
    protected $configFileName = 'packager.yml';

    /**
     * @var string
     */
    protected $configKey = 'packager';

    /**
     * @param IOInterface $io
     * @param string|null $configDir
     * @return array
     */
    public function loadConfiguration(IOInterface $io, string $configDir = null): array
    {

        if (null === $configDir) {
            $configDir = getcwd();
        }

        $configFile = $configDir . DIRECTORY_SEPARATOR . $this->configFileName;

        if (!file_exists($configFile) || !is_readable($configFile)) {
            throw new \RuntimeException('Configuration file ' . $configFile . ' doesn\'t exist or is not readable');
        }

        try {
            $loadedConfig = Yaml::parseFile($configFile);
            $configuration = new Configuration();
            $localConfig = array_key_exists($this->configKey, $loadedConfig) ? $loadedConfig[$this->configKey] : [];

            $config = ConfigProcessor::process($configuration->getConfigTreeBuilder(), $localConfig);
            $config['composer'] = $this->getComposerJsonPath($config['composer']);
            return $config;
        } catch (\RuntimeException $e) {
            $io->writeError($e->getMessage(), true);
            throw $e;
        }
    }

    /**
     * @param string $configDir
     * @param string|null $path
     * @return string
     */
    private function getComposerJsonPath(string $path = null): string
    {
        if(null === $path)
        {
            $path = getcwd().DIRECTORY_SEPARATOR.'composer.json';
        }

        if(!is_file($path) && FALSE === strpos(getcwd(), $path))
        {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
        }

        if(!is_file($path))
        {
            throw new \RuntimeException('Unable to find composer.json at '.$path,500);
        }

        return $path;
    }

}