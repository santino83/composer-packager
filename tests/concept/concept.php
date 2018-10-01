<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 28/09/18
 * Time: 16.35
 */

use Composer\Config;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Json\JsonFile;
use Composer\Package\Package;
use Santino83\ComposerPackager\Archiver\Archive;
use Santino83\ComposerPackager\Repository\ARTIFACTORY\ArtifactoryRepository;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\StringInput;

include __DIR__ . '/../bootstrap.php';

/**
 * CONFIGURATION
 */

$config = [
    'composer' => __DIR__ . '/../../composer.json',
    'name' => 'santino83/composer-packager',
    'repositories' => [
        'ARTIFACTORY' => [
            'endpoint' => ARTIFACTORY_ENDPOINT,
            'username' => ARTIFACTORY_USERNAME,
            'password' => ARTIFACTORY_PASSWORD
        ]
    ]
];

/**
 * LOADS COMPOSER
 */

$input = new StringInput('');
$output = Factory::createOutput();
$helperSet = new HelperSet(array(
    new FormatterHelper(),
    new DebugFormatterHelper(),
    new ProcessHelper(),
    new QuestionHelper(),
));
$io = new ConsoleIO($input, $output, $helperSet);


$composer = Factory::create($io, $config['composer'], false);

/**
 * checks for EXCLUDE in composer.json (DONE COMMAND)
 */
$hasExcludes = $composer->getPackage()->getArchiveExcludes() !== [];
$reflectionClass = new \ReflectionClass($composer->getPackage());

if (!$hasExcludes && $reflectionClass->isSubclassOf(Package::class)) {

    $response = $io->askConfirmation("<question>No archive excludes configured, do you want to add default excludes to your composer.json? (Y/n)</question> ");

    if ($response) {

        $defaults = [
            "/.*",
            "!/.gitignore",
            "/vendor",
            "/tests",
            "/composer.lock",
            "/phpunit.xml",
            "/*.bkp"
        ];

        $composerPath = realpath($config['composer']);
        $jsonFile = new JsonFile($composerPath, null, $io);
        $jsonConfigSource = new Config\JsonConfigSource($jsonFile);
        file_put_contents($composerPath . '.bkp', file_get_contents($composerPath));
        $jsonConfigSource->addProperty('archive', ['exclude' => $defaults]);
        /** $composer->getPackage() must returns an object that MUST be sublcass of Composer\Package\Package  */
        $composer->getPackage()->setArchiveExcludes($defaults);
    }
}


/**
 * creates archive using composer command (DONE COMMAND)
 */

$archiveType = 'zip';
$fileName = 'tmp-' . getmypid();
$actdir = getcwd();
chdir(dirname($config['composer']));
$archiveFile = $composer->getArchiveManager()->archive($composer->getPackage(), $archiveType, $actdir, $fileName);
chdir($actdir);

/**
 * get more info
 */
$rootPackage = $composer->getPackage();

$name = $rootPackage->getName();
if (!$name) {

    $name = $io->askAndValidate('<question>Please enter the name of the package</question> ', function (string $answer = null) {
        if (!is_string($answer) || !trim($answer)) {
            throw new \RuntimeException('No name entered for the package');
        }

        return trim($answer);
    });
}

$version = $rootPackage->getVersion();

if(!$io->askConfirmation('<question>Using current version: '.$version.'? (Y/n)</question> '))
{
    $version = $io->askAndValidate('<question>Please enter the version for the package</question> ', function (string $answer = null) {
        if (!is_string($answer) || !trim($answer)) {
            throw new \RuntimeException('No version entered for the package');
        }

        return trim($answer);
    });

}

if (!$version) {
    $response = $io->ask("<question>Please set a version (def: 1.0.0)</question> ", '1.0.0');
    $version = $response;
}

$isDev = false;
if ($io->askConfirmation('<question>Upload a development version? (N/y)</question> ', false)) {
    $isDev = true;
}

/**
 * upload to artifactory
 */
$archive = new Archive($archiveFile);
$package = new \Santino83\ComposerPackager\Package\Package($name, $version, $archive, $isDev);
$repository = new ArtifactoryRepository();
$result = $repository->publish($package, $config['repositories']['ARTIFACTORY']);
if($result)
{
    $output->writeln('<info>Package published!</info>');
}else{
    $output->writeln('<error>Package not published</error>');
}
