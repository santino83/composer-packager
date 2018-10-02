<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 1.29
 */

namespace Santino83\ComposerPackager\Command;


use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Santino83\ComposerPackager\Utils\ComposerUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckExcludesCommand extends BaseCommand
{

    const NAME = 'check-excludes';

    /**
     * @var array
     */
    protected $alias = ['-a', '--auto-fix'];

    /**
     * @var array
     */
    protected $defaults = [
        "/.*",
        "/.gitignore",
        "/vendor",
        "/tests",
        "/composer.lock",
        "/phpunit.xml"
    ];

    /**
     * @inheritDoc
     */
    protected function configure()
    {

        $this
            ->setName(self::NAME)
            ->setDescription('Checks archive::exclude property in current composer.json')
            ->setDefinition([
                new InputOption('auto-fix', $this->alias, InputOption::VALUE_NONE, 'Adds default exclude patterns if exclude property is not found')
            ]);

    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = $this->getIo();
        $composer = $this->getComposer();
        $autoFix = $input->hasParameterOption($this->alias);

        $package = $composer->getPackage();
        $hasExcludes = $package->getArchiveExcludes();

        $io->write('<info>Excludes ' . ($hasExcludes ? 'are' : 'are not') . ' setted in composer.json</info>', true);

        if ($hasExcludes) {
            $this->logger->debug('Exclude property found, nothing to do');
            return;
        }

        if ($autoFix || $io->askConfirmation("<question>No archive excludes configured, do you want to add default excludes to your composer.json? (Y/n)</question> ")) {
            $this->fixComposerJson($package);
        }

    }

    /**
     * @param PackageInterface $package
     */
    private function fixComposerJson(PackageInterface $package)
    {
        if (!($package instanceof Package)) {
            throw new \RuntimeException('Package must be instance of ' . Package::class);
        }

        $config = $this->getConfig();
        $io = $this->getIo();

        $this->logger->debug('Updating ' . $config['composer']);

        $jsonConfigSource = ComposerUtils::getConfig($config['composer'], $io);

        $this->logger->debug('Adding default excludes: ' . implode(',', $this->defaults));
        $jsonConfigSource->addProperty('archive', ['exclude' => $this->defaults]);

        $this->logger->debug('Updating running package instance');
        $package->setArchiveExcludes($this->defaults);
        $io->write('<info>Archive excludes configured</info>', true);
    }


}