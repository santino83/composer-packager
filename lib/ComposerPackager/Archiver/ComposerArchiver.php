<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 1.15
 */

namespace Santino83\ComposerPackager\Archiver;

use Composer\Composer;
use Psr\Log\LoggerInterface;
use Santino83\ComposerPackager\Log\NullLogger;

class ComposerArchiver implements ArchiverInterface
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
     * ComposerArchiver constructor.
     * @param Composer $composer
     */
    public function __construct(Composer $composer, LoggerInterface $logger = null)
    {
        $this->composer = $composer;

        $this->logger = $logger;
        if(null === $this->logger)
        {
            $this->logger = new NullLogger();
        }
    }

    /**
     * @inheritdoc
     */
    public function archive(array $config): ArchiveInterface
    {
        $archiveFormat = $config['archive-format'];
        $fileName = 'archive-'.getmypid().'-'.time();
        $targetDir = sys_get_temp_dir();

        $package = $this->composer->getPackage();
        $archiveManager = $this->composer->getArchiveManager();

        $this->logger->debug('creating archive {archive} in {targetDir} with format {format}',['archive' => $fileName, 'targetDir' => $targetDir, 'format' => $archiveFormat]);
        $archiveFile = $archiveManager->archive($package, $archiveFormat, $targetDir, $fileName, false);
        return new Archive($archiveFile);
    }

    /**
     * @return Composer
     */
    public function getComposer(): Composer
    {
        return $this->composer;
    }

}