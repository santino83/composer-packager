<?php

namespace Santino83\ComposerPackager;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\IO\IOInterface;
use Composer\Util\ErrorHandler;
use Psr\Log\LoggerInterface;
use Santino83\ComposerPackager\Command\ArchiveCommand;
use Santino83\ComposerPackager\Command\CheckExcludesCommand;
use Santino83\ComposerPackager\Command\PublishCommand;
use Santino83\ComposerPackager\Config\ConfigLoader;
use Santino83\ComposerPackager\Log\ConsoleLogger;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Packager extends BaseApplication
{
    private static $logo = '
    
$$$$$$$\   $$$$$$\   $$$$$$\  $$\   $$\  $$$$$$\   $$$$$$\  $$$$$$$$\ $$$$$$$\  
$$  __$$\ $$  __$$\ $$  __$$\ $$ | $$  |$$  __$$\ $$  __$$\ $$  _____|$$  __$$\ 
$$ |  $$ |$$ /  $$ |$$ /  \__|$$ |$$  / $$ /  $$ |$$ /  \__|$$ |      $$ |  $$ |
$$$$$$$  |$$$$$$$$ |$$ |      $$$$$  /  $$$$$$$$ |$$ |$$$$\ $$$$$\    $$$$$$$  |
$$  ____/ $$  __$$ |$$ |      $$  $$<   $$  __$$ |$$ |\_$$ |$$  __|   $$  __$$< 
$$ |      $$ |  $$ |$$ |  $$\ $$ |\$$\  $$ |  $$ |$$ |  $$ |$$ |      $$ |  $$ |
$$ |      $$ |  $$ |\$$$$$$  |$$ | \$$\ $$ |  $$ |\$$$$$$  |$$$$$$$$\ $$ |  $$ |
\__|      \__|  \__| \______/ \__|  \__|\__|  \__| \______/ \________|\__|  \__|      
                                                                      
    ';

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool 
     */
    protected $useDefaults = false;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct('Packager', '1.0');
    }

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if(null === $output)
        {
            $output = Factory::createOutput();
        }

        return parent::run($input, $output);
    }

    /**
     * @inheritDoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $io = $this->io = new ConsoleIO($input, $output, $this->getHelperSet());
        ErrorHandler::register($io);

        // switch working dir
        if ($newWorkDir = $this->getNewWorkingDir($input)) {
            $oldWorkingDir = getcwd();
            chdir($newWorkDir);
            $io->writeError('Changed CWD to ' . getcwd(), true, IOInterface::DEBUG);
        }

        $this->useDefaults = $input->hasParameterOption(['--use-defaults','-U'], false);
        $this->loadConfiguration($input);

        try {
            $result = parent::doRun($input, $output);

            if (isset($oldWorkingDir)) {
                chdir($oldWorkingDir);
            }

            return $result;
        }catch (\Throwable $e){
            throw new \RuntimeException('Error occurred', $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getHelp()
    {
        return self::$logo . parent::getHelp();
    }

    /**
     * @return Composer
     */
    public function getComposer(): Composer
    {
        if (null === $this->composer) {
            try {
                $this->composer = Factory::create($this->io, $this->getConfiguration()['composer'], false);
            } catch (\Exception $e) {
                $this->io->writeError($e->getMessage());
                exit(1);
            }
        }

        return $this->composer;
    }

    /**
     * @return IOInterface
     */
    public function getIO(): IOInterface
    {
        return $this->io;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if(null === $this->logger)
        {
            $this->logger = new ConsoleLogger($this->io);
        }

        return $this->logger;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isUseDefaults(): bool
    {
        return $this->useDefaults;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new CheckExcludesCommand(),
            new ArchiveCommand(),
            new PublishCommand()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('--working-dir', '-d', InputOption::VALUE_REQUIRED, 'If specified, use the given directory as working directory.'));
        $definition->addOption(new InputOption('--config', '-c', InputOption::VALUE_REQUIRED, 'If specified, use the given directory to looking for packager.yml.'));
        $definition->addOption(new InputOption('--use-default', '-U', InputOption::VALUE_NONE,'Use default values (default: false)'));

        return $definition;
    }

    /**
     * @param InputInterface $input
     */
    private function loadConfiguration(InputInterface $input)
    {
        $io = $this->getIO();

        $configDir = getcwd();
        if($newConfigDir = $this->getConfigDir($input))
        {
            $configDir = $newConfigDir;
            $io->writeError('Changed config directory to ' . $configDir, true, IOInterface::DEBUG);
        }

        $loader = new ConfigLoader();
        $this->config = $loader->loadConfiguration($io, $configDir);
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     * @return string
     */
    private function getNewWorkingDir(InputInterface $input): string
    {
        $workingDir = $input->getParameterOption(array('--working-dir', '-d'));
        if (false !== $workingDir && !is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory specified, '.$workingDir.' does not exist.');
        }

        return $workingDir;
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     * @return string
     */
    private function getConfigDir(InputInterface $input): string
    {
        $configDir = $input->getParameterOption(array('--config','-c'));
        if (false !== $configDir && !is_dir($configDir)) {
            throw new \RuntimeException('Invalid config directory specified, '.$configDir.' does not exist.');
        }

        return $configDir;
    }

}
