<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 1.06
 */

namespace Santino83\ComposerPackager\Command;


use Composer\Composer;
use Composer\IO\IOInterface;
use Psr\Log\LoggerInterface;
use Santino83\ComposerPackager\Log\NullLogger;
use Santino83\ComposerPackager\Packager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $useDefaults = false;

    /**
     * @var bool
     */
    private $useDefaultsProcessed = false;

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getLogger();
        parent::initialize($input, $output);
    }

    /**
     * @return Composer
     */
    public function getComposer(): Composer
    {
        if(null === $this->composer)
        {
            $application = $this->getApplication();
            if(!($application instanceof Packager))
            {
                throw new \RuntimeException('Invalid application, cannot load composer. Configure this command manually');
            }
            $this->composer = $application->getComposer();
        }

        return $this->composer;
    }

    /**
     * @param Composer $composer
     * @return BaseCommand
     */
    public function setComposer(Composer $composer): BaseCommand
    {
        $this->composer = $composer;
        return $this;
    }

    /**
     * @return IOInterface
     */
    public function getIo(): IOInterface
    {
        if(null === $this->io)
        {
            $application = $this->getApplication();
            if(!($application instanceof Packager))
            {
                throw new \RuntimeException('Invalid application, cannot load IO interface. Configure this command manually');
            }
            $this->io = $application->getIO();
        }

        return $this->io;
    }

    /**
     * @param IOInterface $io
     * @return BaseCommand
     */
    public function setIo(IOInterface $io): BaseCommand
    {
        $this->io = $io;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        if(!$this->config)
        {
            $application = $this->getApplication();
            if(!($application instanceof Packager))
            {
                throw new \RuntimeException('Invalid application, cannot load configuration. Configure this command manually');
            }
            $this->config = $application->getConfiguration();
        }

        return $this->config;
    }

    /**
     * @param array $config
     * @return BaseCommand
     */
    public function setConfig(array $config): BaseCommand
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if(null === $this->logger)
        {
            $application = $this->getApplication();
            if(!($application instanceof Packager))
            {
                $this->logger = new NullLogger();
            }else{
                $this->logger = $application->getLogger();
            }
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return BaseCommand
     */
    public function setLogger(LoggerInterface $logger): BaseCommand
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseDefaults(): bool
    {
        if(false === $this->useDefaultsProcessed)
        {
            $this->setUseDefaults(($this->getApplication() instanceof Packager) ? $this->getApplication()->isUseDefaults() : false);
        }

        return $this->useDefaults;
    }

    /**
     * @param bool $useDefaults
     * @return BaseCommand
     */
    public function setUseDefaults(bool $useDefaults): BaseCommand
    {
        $this->useDefaults = $useDefaults;
        $this->useDefaultsProcessed = true;
        return $this;
    }

}