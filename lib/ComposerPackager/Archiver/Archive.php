<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 06/07/18
 * Time: 17.49
 */

namespace Santino83\ComposerPackager\Archiver;

/**
 * Class Archive
 * @package Santino83\ComposerPackager\Archiver
 */
class Archive implements ArchiveInterface
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $exten;

    /**
     * Archive constructor.
     * @param string $path full path of the archive including name and extension
     */
    public function __construct(string $path)
    {
        $this->path = $path;

        $pathinfo = pathinfo($path);
        $this->name = $pathinfo['filename'];
        $this->exten = $pathinfo['extension'];
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
    public function getType(): string
    {
        return $this->exten;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }


}