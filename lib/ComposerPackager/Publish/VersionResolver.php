<?php


/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 13.49
 */

namespace Santino83\ComposerPackager\Publish;


use Composer\Composer;
use Gitonomy\Git\Reference\Tag;
use Gitonomy\Git\Repository;
use Psr\Log\LoggerInterface;
use Santino83\ComposerPackager\Log\NullLogger;

class VersionResolver implements VersionResolverInterface
{

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * VersionResolver constructor.
     * @param Composer $composer
     * @param LoggerInterface|null $logger
     */
    public function __construct(Composer $composer, LoggerInterface $logger = null)
    {
        $this->composer = $composer;
        $this->logger = $logger ? $logger : new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function resolve(array $config): string
    {
        $extractors = ['array', 'git', 'composer'];
        $version = '';

        foreach ($extractors as $extractorType) {
            $version = $this->{'from' . $extractorType}($config);
            if ($version) {
                break;
            }
        }

        return $version;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param array $config
     * @return string
     */
    protected function fromArray(array $config): string
    {
        return $config['version'] ? $config['version'] : '';
    }

    /**
     * @param array $config
     * @return string
     */
    protected function fromComposer(array $config): string
    {
        $rootPackage = $this->composer->getPackage();

        return $rootPackage->getVersion();
    }

    /**
     * @param array $config
     * @return string
     */
    protected function fromGit(array $config): string
    {
        $repoPath = dirname(realpath($config['composer']));

        if (!is_dir($repoPath . DIRECTORY_SEPARATOR . '.git')) {
            $this->logger->debug('No GIT Repository found at ' . $repoPath);
            return '';
        }

        $repository = new Repository($repoPath, ['logger' => $this->logger]);
        $tags = $repository->getReferences()->getTags();

        if (!$tags) {
            return '';
        }

        $tag = array_pop($tags);
        /**@var $tag Tag */

        return $tag->getName();
    }

}