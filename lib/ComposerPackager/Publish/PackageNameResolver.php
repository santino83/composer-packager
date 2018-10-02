<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 02/10/18
 * Time: 11.52
 */

namespace Santino83\ComposerPackager\Publish;


use Composer\Composer;
use Psr\Log\LoggerInterface;
use Santino83\ComposerPackager\Log\NullLogger;

class PackageNameResolver implements PackageNameResolverInterface
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
     * PackageNameResolver constructor.
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
        return $config['name'] ? $config['name'] : $this->composer->getPackage()->getName();
    }

}