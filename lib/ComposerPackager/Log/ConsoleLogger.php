<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 01/10/18
 * Time: 11.40
 */

namespace Santino83\ComposerPackager\Log;


use Composer\IO\IOInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ConsoleLogger implements LoggerInterface
{

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * ConsoleLogger constructor.
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<error>%s</error>',$message,$context), IOInterface::QUIET);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<bg=yellow;options=bold;fg=red>%s</>',$message,$context), IOInterface::QUIET);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<error>%s</error>',$message,$context), IOInterface::QUIET);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<error>%s</error>',$message,$context), IOInterface::NORMAL);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<bg=yellow;options=bold;fg=red>%s</>',$message,$context), IOInterface::VERBOSE);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<comment>%s</comment>',$message,$context), IOInterface::VERBOSE);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<info>%s</info>',$message,$context), IOInterface::NORMAL);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        switch ($level) {
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::DEBUG:
            case LogLevel::EMERGENCY:
            case LogLevel::ERROR:
            case LogLevel::INFO:
            case LogLevel::NOTICE:
            case LogLevel::WARNING:
                $this->{$level}($message, $context);
                break;
            default:
                $this->debug($message, $context);
        }
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        $this->doLog($this->formatMessage('<comment>%s</comment>',$message,$context), IOInterface::DEBUG);
    }

    /**
     * @param string $formattedMessage
     * @param int $level
     */
    private function doLog(string $formattedMessage, int $level)
    {
        $this->io->write($formattedMessage, true, $level);
    }

    /**
     * @param string $format
     * @param string $message
     * @param array $context
     * @return string
     */
    private function formatMessage(string $format, string $message, array $context = []): string
    {
        return sprintf($format, $this->processMessage($message, $context));
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    private function processMessage(string $message, array $context = []): string
    {
        if (false === strpos($message, '{')) {
            return $message;
        }

        $replacements = array();
        foreach ($context as $key => $val) {
            if (is_null($val) || is_scalar($val) || (is_object($val) && method_exists($val, "__toString"))) {
                $replacements['{' . $key . '}'] = $val;
            } elseif (is_object($val)) {
                $replacements['{' . $key . '}'] = '[object ' . get_class($val) . ']';
            } else {
                $replacements['{' . $key . '}'] = '[' . gettype($val) . ']';
            }
        }

        return strtr($message, $replacements);
    }

}