<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 13.37
 */

namespace Santino83\ComposerPackager\Command;


use Composer\IO\IOInterface;
use Santino83\ComposerPackager\Archiver\ComposerArchiver;
use Santino83\ComposerPackager\Package\Package;
use Santino83\ComposerPackager\Publish\PackageNameResolver;
use Santino83\ComposerPackager\Publish\VersionResolver;
use Santino83\ComposerPackager\Repository\RepositoryLoader;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommand extends BaseCommand
{

    const NAME = 'publish';

    private $alias = [
        'ov' => ['-R'],
        'name' => ['-N'],
        'dev' => ['-D']
    ];

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Publish this project to remote repositories')
            ->setDefinition([
                new InputOption("release-version", $this->alias['ov'], InputOption::VALUE_REQUIRED, 'Specify a release version (override auto-detection)', ''),
                new InputOption("name", $this->alias['name'], InputOption::VALUE_REQUIRED, 'Specify a package name (override composer.json)', ''),
                new InputOption('dev', $this->alias['dev'], InputOption::VALUE_NONE, 'Specify it is a development version')
            ]);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIo();
        $config = $this->getMergedConfiguration($input);

        $checkExcludesCommand = $this->getApplication()->find(CheckExcludesCommand::NAME);
        $argv = new ArrayInput(['command' => CheckExcludesCommand::NAME]);

        $checkExcludesCommand->run($argv, $output);

        $archiver = new ComposerArchiver($this->getComposer(), $this->getLogger());
        $nameResolver = new PackageNameResolver($this->getComposer(), $this->getLogger());
        $versionResolver = new VersionResolver($this->getComposer(), $this->getLogger());

        $name = $nameResolver->resolve($config);
        $version = $versionResolver->resolve($config);
        $archive = $archiver->archive($config);

        $question = sprintf('<question>Publish package as %s at %s (%s version)? (Y/n)</question> ',$name, $version, $config['dev'] ? 'development' : 'no development');
        if(false === $io->askConfirmation($question, true))
        {
            $config['name'] = $name = $this->askForName($name);
            $config['version'] = $version = $this->askForVersion($version);
            $config['dev'] = $this->askForIsDev($config['dev']);
            $this->setConfig($config);
        }

        $this->logger->debug("Publish $name at version $version (".($config['dev'] ? 'dev' : 'prod').')');

        $package = new Package($name, $version, $archive, $config['dev']);

        $repoLoader = new RepositoryLoader();

        foreach($config['repositories'] as $type => $configRepo)
        {
            try{
                $repo = $repoLoader->load($type);
            }catch (\RuntimeException $e){
                $io->writeError('Unable to load repository: '.$e->getMessage(), true, IOInterface::NORMAL);
                continue;
            }

            $result = $repo->publish($package, $configRepo);
            if($result)
            {
                $io->write('<info>Package published to '.$type.'</info>');
            }else{
                $io->writeError('<error>Cannot publish to '.$type.'</error>');
            }
        }

        $io->write('<info>Done!</info>');
    }

    /**
     * @param string $currentName
     * @return string
     * @throws \Exception
     */
    private function askForName(string $currentName):string
    {
        $io = $this->getIo();
        if(false === $io->askConfirmation('<question>Use package name '.$currentName.'? (Y/n)</question> ', true))
        {
            return $io->askAndValidate('<question>Insert a package name</question> ',function($answer){
               if(!trim($answer)){
                   throw new \RuntimeException('Please insert a name');
               }

               return trim($answer);
            });
        }

        return $currentName;
    }

    /**
     * @param string $currentVersion
     * @return string
     * @throws \Exception
     */
    private function askForVersion(string $currentVersion):string
    {
        $io = $this->getIo();
        if(false === $io->askConfirmation('<question>Use version '.$currentVersion.'? (Y/n)</question> ', true))
        {
            return $io->askAndValidate('<question>Insert a version</question> ',function($answer){
                if(!trim($answer)){
                    throw new \RuntimeException('Please insert a version');
                }

                return trim($answer);
            });
        }

        return $currentVersion;
    }

    /**
     * @param bool $currentIsDev
     * @return bool
     */
    private function askForIsDev(bool $currentIsDev): bool
    {
        $label = $currentIsDev ? '(Y/n)' :  '(y/N)';
        $io = $this->getIo();
        return $io->askConfirmation('<question>Is a development version? '.$label.'</question> ', $currentIsDev);
    }

    /**
     * Gets the configuration after merged input values
     *
     * @param InputInterface $input
     * @return array
     */
    private function getMergedConfiguration(InputInterface $input): array
    {
        $version = $input->getOption('release-version');
        $name = $input->getOption('name');
        $isDev = $input->hasParameterOption(['--dev','-D']);

        $config = $this->getConfig();
        $config['name'] = $name ? $name : $config['version'];
        $config['version'] = $version ? $version : $config['version'];
        $config['dev'] = $config['dev'] || $isDev;

        $this->setConfig($config);
        return $config;
    }

}