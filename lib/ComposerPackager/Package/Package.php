<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 06/07/18
 * Time: 17.42
 */

namespace Santino83\ComposerPackager\Package;

use Santino83\ComposerPackager\Archiver\ArchiveInterface;

class Package implements PackageInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $isDev = false;

    /**
     * @var ArchiveInterface
     */
    private $archive;

    /**
     * Package constructor.
     * @param string $name
     * @param string $version     *
     * @param ArchiveInterface $archive
     * @param bool $isDev
     */
    public function __construct(string $name, string $version = null, ArchiveInterface $archive = null, bool $isDev = false)
    {
        $this->name = $name;
        $this->version = $version;
        $this->isDev = $isDev;
        $this->archive = $archive;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return $this->version.($this->isDev() ? '-'.time() : '');
    }

    /**
     * @inheritDoc
     */
    public function isDev(): bool
    {
        return $this->isDev;
    }

    /**
     * @inheritDoc
     */
    public function getArchive(): ArchiveInterface
    {
        return $this->archive;
    }

    /**
     * @param string $name
     * @return Package
     */
    public function setName(string $name): Package
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $version
     * @return Package
     */
    public function setVersion(string $version): Package
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @param bool $isDev
     * @return Package
     */
    public function setIsDev(bool $isDev): Package
    {
        $this->isDev = $isDev;
        return $this;
    }

    /**
     * @param ArchiveInterface $archive
     * @return Package
     */
    public function setArchive(ArchiveInterface $archive): Package
    {
        $this->archive = $archive;
        return $this;
    }

}