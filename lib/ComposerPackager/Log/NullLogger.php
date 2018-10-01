<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 13.04
 */

namespace Santino83\ComposerPackager\Log;


use Psr\Log\LoggerInterface;

class NullLogger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array())
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        // do nothing
    }


}