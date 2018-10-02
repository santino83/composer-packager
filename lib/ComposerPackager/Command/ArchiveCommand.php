<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 13.23
 */

namespace Santino83\ComposerPackager\Command;


use Santino83\ComposerPackager\Archiver\ArchiverInterface;
use Santino83\ComposerPackager\Archiver\ComposerArchiver;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveCommand extends BaseCommand
{

    const NAME = 'archive';

    /**
     * @var ArchiverInterface
     */
    private $archiver;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Creates a composer archive of the current project');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checkExcludesCommand = $this->getApplication()->find(CheckExcludesCommand::NAME);
        $argv = new ArrayInput(['command' => CheckExcludesCommand::NAME]);

        $checkExcludesCommand->run($argv, $output);

        $archiver = $this->getArchiver();
        $archive = $archiver->archive($this->getConfig());
        $this->getIo()->write('<info>Archive created at '.$archive->getPath().'</info>', true);
    }

    /**
     * @return ArchiverInterface
     */
    protected function getArchiver(): ArchiverInterface
    {
        if (null === $this->archiver) {
            $this->archiver = new ComposerArchiver($this->getComposer(), $this->getLogger());
        }

        return $this->archiver;
    }

    /**
     * @param ArchiverInterface $archiver
     */
    public function setArchiver(ArchiverInterface $archiver): void
    {
        $this->archiver = $archiver;
    }

}