<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/9/7
 * Time: 00:15
 */

namespace sinri\ark\core;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class ArkLogger extends AbstractLogger
{
    /**
     * @var null|string Give the log storage directory, if null, output to STDOUT
     */
    protected $targetLogDir = null;
    /**
     * @var string|callable
     */
    protected $prefix = '';
    /**
     * @var string values as LogLevel::LEVEL
     */
    protected $ignoreLevel;

    /**
     * @return string
     */
    public function getIgnoreLevel(): string
    {
        return $this->ignoreLevel;
    }

    /**
     * @var bool if keep completely silent
     */
    protected $silent = false;
    /**
     * @var bool If show PID within every log row
     */
    protected $showProcessID = false;
    /**
     * @var string|null string should follow Date Format, and null for no rotating
     * @since 2.2
     */
    protected $rotateTimeFormat = "Y-m-d";

    /**
     * @var ArkLoggerAbstractBuffer
     * @since 2.3 supported ArkLogBuffer
     * @since 2.6 switched to ArkLoggerAbstractBuffer
     */
    protected $buffer = null;
    /**
     * If true, the log files with same prefix would be put into a directory named with prefix
     * @var bool
     * @since 2.5
     */
    protected $groupByPrefix = false;

    /**
     * ArkLogger constructor.
     * @param null $targetLogDir
     * @param string|callable $prefix
     * @param string|null $rotateTimeFormat string should follow Date Format, and null for no rotating @since 2.2
     * @param null|ArkLoggerAbstractBuffer $buffer if null, buffer off @since 2.3 @since 2.6 switched to ArkLoggerAbstractBuffer
     * @param bool $groupByPrefix If true, the log files with same prefix would be put into a directory named with prefix
     */
    public function __construct($targetLogDir = null, $prefix = '', $rotateTimeFormat = 'Y-m-d', $buffer = null, $groupByPrefix = false)
    {
        $this->targetLogDir = $targetLogDir;
        $this->setPrefix($prefix);
        $this->ignoreLevel = LogLevel::INFO;
        $this->showProcessID = false;
        $this->rotateTimeFormat = $rotateTimeFormat;
        $this->buffer = $buffer;
        $this->groupByPrefix = $groupByPrefix;
    }

    /**
     * @param string|callable $prefix
     * @return ArkLogger
     */
    public function setPrefix($prefix)
    {

        if (is_callable($prefix)) {
            $this->prefix = $prefix;
        } elseif ($prefix !== '') {
            $prefix = self::normalizePrefix($prefix);
            // Observed case that forked child process would die here, reason unknown
        }
        $this->prefix = $prefix;

        return $this;
    }

    public static function normalizePrefix($rawPrefix)
    {
        return preg_replace('/[^A-Za-z0-9]/', '_', $rawPrefix);
    }

    /**
     * @return ArkLogger
     */
    public static function makeSilentLogger()
    {
        $logger = new ArkLogger();
        $logger->silent = true;
        return $logger;
    }

    /**
     * @return bool
     */
    public function isGroupByPrefix(): bool
    {
        return $this->groupByPrefix;
    }

    /**
     * @param bool $groupByPrefix
     * @return ArkLogger
     */
    public function setGroupByPrefix(bool $groupByPrefix): ArkLogger
    {
        $this->groupByPrefix = $groupByPrefix;
        return $this;
    }

    /**
     * @return ArkLoggerAbstractBuffer
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * @param ArkLoggerAbstractBuffer $buffer
     * @return ArkLogger
     * @since 2.3
     */
    public function setBuffer($buffer)
    {
        $this->buffer = $buffer;
        return $this;
    }

    /**
     * @param string $rotateTimeFormat
     * @return ArkLogger
     * @since 2.2
     */
    public function setRotateTimeFormat(string $rotateTimeFormat)
    {
        $this->rotateTimeFormat = $rotateTimeFormat;
        return $this;
    }

    /**
     * @param bool $showProcessID
     * @return ArkLogger
     */
    public function setShowProcessID(bool $showProcessID)
    {
        $this->showProcessID = $showProcessID;
        return $this;
    }

    /**
     * @param null $targetLogDir
     * @return ArkLogger
     */
    public function setTargetLogDir($targetLogDir)
    {
        $this->targetLogDir = $targetLogDir;
        return $this;
    }

    /**
     * @param string $ignoreLevel this level and above would be visible
     * @return ArkLogger
     */
    public function setIgnoreLevel($ignoreLevel)
    {
        $this->ignoreLevel = $ignoreLevel;
        return $this;
    }

    /**
     * @param $message
     * @return ArkLogger
     * @since 2.1
     * Might be used in showing progress
     * Without any DATE or CONTEXT but raw MESSAGE as string, even no tail/lead space
     */
    public function logInline($message)
    {
        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $message;
            return $this;
        }
        @file_put_contents($target_file, $message, FILE_APPEND);
        return $this;
    }

    /**
     * Return the target file path which log would be written into.
     * If target log directory not set, return false.
     * @return bool|string
     */
    protected function decideTargetFile()
    {
        if (empty($this->targetLogDir)) {
            return false;
        }
        if (!file_exists($this->targetLogDir)) {
            @mkdir($this->targetLogDir, 0777, true);
        }

        return $this->getCurrentLogFilePath();
    }

    /**
     * Sometime you may need to know where the log file is
     * @return string
     * @since 2.2
     * @since 2.5 Add group by prefix support
     */
    public function getCurrentLogFilePath()
    {
        $rotateTimeMark = "";
        if ($this->rotateTimeFormat !== null) {
            $rotateTimeMark .= "-" . date($this->rotateTimeFormat);
        }

        if (is_callable($this->prefix)) {
            $prefix = call_user_func_array($this->prefix, []);
            // [del]not check prefix here, let user ensure this correctness[/del]
            $prefix = self::normalizePrefix($prefix);
            // I thought again and add this check...
        } else {
            $prefix = $this->prefix;
        }

        $dir = $this->targetLogDir;
        $file = 'log' . (empty($prefix) ? '' : "-" . $prefix) . $rotateTimeMark . '.log';

        if ($this->groupByPrefix) {
            if ($prefix === '') {
                $dir = $this->targetLogDir . DIRECTORY_SEPARATOR . 'default-log';
                $file = 'log' . $rotateTimeMark . '.log';
            } else {
                $dir = $this->targetLogDir . DIRECTORY_SEPARATOR . $prefix;
                $file = 'log' . "-" . $prefix . $rotateTimeMark . '.log';
            }
        }

        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }

        return $dir . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * If you want to output log directly to STDOUT, use this.
     * @param $level
     * @param $message
     * @param array $context
     * @param bool $enforceEndOfLine @since 2.1
     * @return ArkLogger
     * @since 2.0 renamed from echo to print
     */
    public function print($level, $message, array $context = array(), $enforceEndOfLine = true)
    {
        if ($this->shouldIgnoreThisLog($level)) {
            return $this;
        }
        $msg = $this->generateLog($level, $message, $context, $enforceEndOfLine);
        echo $msg;

        return $this;
    }

    /**
     * @param $level
     * @return bool
     */
    protected function shouldIgnoreThisLog($level)
    {
        if ($this->silent) return true;
        return !self::isLevelHighEnough($this->ignoreLevel, $level);
        /*
        static $levelValue = [
            LogLevel::EMERGENCY => 7,
            LogLevel::ALERT => 6,
            LogLevel::CRITICAL => 5,
            LogLevel::ERROR => 4,
            LogLevel::WARNING => 3,
            LogLevel::NOTICE => 2,
            LogLevel::INFO => 1,
            LogLevel::DEBUG => 0,
        ];
        $coming = ArkHelper::readTarget($levelValue, $level, 1);
        $limit = ArkHelper::readTarget($levelValue, $this->ignoreLevel, 0);
        if ($coming < $limit) {
            return true;
        }
        return false;
        */
    }

    /**
     * @param string $ignoreLevel the lowest visible level
     * @param string $level
     * @return bool
     * @since 2.6.3
     */
    public static function isLevelHighEnough($ignoreLevel, $level)
    {
        static $levelValue = [
            LogLevel::EMERGENCY => 7,
            LogLevel::ALERT => 6,
            LogLevel::CRITICAL => 5,
            LogLevel::ERROR => 4,
            LogLevel::WARNING => 3,
            LogLevel::NOTICE => 2,
            LogLevel::INFO => 1,
            LogLevel::DEBUG => 0,
        ];
        $coming = ArkHelper::readTarget($levelValue, $level, 1);
        $limit = ArkHelper::readTarget($levelValue, $ignoreLevel, 0);
        return ($coming >= $limit);
    }

    /**
     * Return the string format log content
     * @param $level
     * @param $message
     * @param string|array $object
     * @param bool $enforceEndOfLine @since 2.1
     * @param string $logBody @since 2.3
     * @return string
     */
    protected function generateLog($level, $message, $object = '', $enforceEndOfLine = true, &$logBody = "")
    {
        $now = date('Y-m-d H:i:s');
        $level_string = "[{$level}]";
        $logHead = "{$now} {$level_string}";

        $logBody = "";
        if ($this->showProcessID) {
            $logBody .= "PID: " . getmypid() . " ";
        }
        $logBody .= "{$message} |" . (is_string($object) ? $object : json_encode($object, JSON_UNESCAPED_UNICODE));

        return $logHead . " " . $logBody . ($enforceEndOfLine ? PHP_EOL : "");
    }

    /**
     * @param bool $assert
     * @param string $messageForTrue
     * @param array|null $contextForTrue
     * @param string $messageForFalse
     * @param array|null $contextForFalse
     * @param string $levelForTrue
     * @param string $levelForFalse
     * @return ArkLogger
     * @since 2.4 this is a experimental function
     */
    public function smartLog($assert, $messageForTrue = "OK", array $contextForTrue = null, $messageForFalse = "ERROR", array $contextForFalse = null, $levelForTrue = LogLevel::INFO, $levelForFalse = LogLevel::ERROR)
    {
        if ($assert) {
            if ($contextForTrue === null) {
                $contextForTrue = ['assert' => $assert];
            }
            $this->log($levelForTrue, $messageForTrue, $contextForTrue);
        } else {
            if ($contextForFalse === null) {
                $contextForFalse = ['assert' => $assert];
            }
            $this->log($levelForFalse, $messageForFalse, $contextForFalse);
        }

        return $this;
    }

    /**
     * If enabled buffer, also output to buffer @param mixed $level
     * @param string $message
     * @param array $context
     * @return ArkLogger
     * @since 2.3
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->shouldIgnoreThisLog($level)) {
            return $this;
        }
        $msg = $this->generateLog($level, $message, $context, true, $body);

        if ($this->buffer !== null) {
            $this->buffer->appendRaw($level, $body);
            if ($this->buffer->isBufferOnly()) {
                return $this;
            }
        }

        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $msg;
            return $this;
        }
        @file_put_contents($target_file, $msg, FILE_APPEND);

        return $this;
    }

    /**
     * @param bool $assert
     * @param string $message
     * @param array $context
     * @return ArkLogger
     * @since 2.4 this is a experimental function
     */
    public function smartLogLite($assert, $message = "", array $context = [])
    {
        if ($assert) {
            $this->info("Assert True. " . $message, $context);
        } else {
            $this->error("Assert False. " . $message, $context);
        }
        return $this;
    }

    /**
     * It is better use this when ROTATE function is disabled.
     * @return bool
     * @since 2.6
     */
    public function removeCurrentLogFile()
    {
        $file = $this->decideTargetFile();
        return @unlink($file);
    }

    /**
     * @param string $command
     * @param array $meta
     * @since 2.6
     */
    public function sendCommandToBuffer($command, $meta = [])
    {
        if ($this->buffer !== null) {
            $this->buffer->whenCommandComesFromLogger($command, $meta);
        }
    }
}