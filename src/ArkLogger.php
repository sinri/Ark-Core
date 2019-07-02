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
    protected $targetLogDir = null;
    /**
     * @var string|callable
     */
    protected $prefix = '';
    protected $ignoreLevel;
    protected $silent = false;
    protected $showProcessID = false;
    /**
     * @var string|null string should follow Date Format, and null for no rotating
     * @since 2.2
     */
    protected $rotateTimeFormat = "Y-m-d";

    /**
     * @var ArkLoggerBuffer
     * @since 2.3
     */
    protected $buffer = null;

    /**
     * @return ArkLoggerBuffer
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * @param ArkLoggerBuffer $buffer
     * @since 2.3
     */
    public function setBuffer(ArkLoggerBuffer $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * ArkLogger constructor.
     * @param null $targetLogDir
     * @param string|callable $prefix
     * @param string|null $rotateTimeFormat string should follow Date Format, and null for no rotating @since 2.2
     * @param null|ArkLoggerBuffer $buffer if null, buffer off @since 2.3
     */
    public function __construct($targetLogDir = null, $prefix = '', $rotateTimeFormat = 'Y-m-d', $buffer = null)
    {
        $this->targetLogDir = $targetLogDir;
        $this->setPrefix($prefix);
        $this->ignoreLevel = LogLevel::INFO;
        $this->showProcessID = false;
        $this->rotateTimeFormat = $rotateTimeFormat;
        $this->buffer = $buffer;
    }

    /**
     * @param string|callable $prefix
     */
    public function setPrefix($prefix)
    {
        if (is_callable($prefix)) {
            $this->prefix = $prefix;
            return;
        }
        if ($prefix !== '') {
            $prefix = preg_replace('/[^A-Za-z0-9]/', '_', $prefix);
            // Observed case that forked child process would die here, reason unknown
        }
        $this->prefix = $prefix;
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
     * @param string $rotateTimeFormat
     * @since 2.2
     */
    public function setRotateTimeFormat(string $rotateTimeFormat)
    {
        $this->rotateTimeFormat = $rotateTimeFormat;
    }

    /**
     * @param bool $showProcessID
     */
    public function setShowProcessID(bool $showProcessID)
    {
        $this->showProcessID = $showProcessID;
    }

    /**
     * @param null $targetLogDir
     */
    public function setTargetLogDir($targetLogDir)
    {
        $this->targetLogDir = $targetLogDir;
    }

    /**
     * @param string $ignoreLevel this level and above would be visible
     */
    public function setIgnoreLevel($ignoreLevel)
    {
        $this->ignoreLevel = $ignoreLevel;
    }

    /**
     * If enabled buffer, also output to buffer @param mixed $level
     * @param string $message
     * @param array $context
     *@since 2.3
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->shouldIgnoreThisLog($level)) {
            return;
        }
        $msg = $this->generateLog($level, $message, $context, true, $body);

        if ($this->buffer !== null) {
            $this->buffer->appendRaw($level, $body);
            if ($this->buffer->isBufferOnly()) {
                return;
            }
        }

        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $msg;
            return;
        }
        @file_put_contents($target_file, $msg, FILE_APPEND);
    }

    /**
     * @param $level
     * @return bool
     */
    protected function shouldIgnoreThisLog($level)
    {
        if ($this->silent) return true;
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
     */
    public function getCurrentLogFilePath()
    {
        $rotateTimeMark = "";
        if ($this->rotateTimeFormat !== null) {
            $rotateTimeMark .= "-" . date($this->rotateTimeFormat);
        }

        if (is_callable($this->prefix)) {
            $prefix = call_user_func_array($this->prefix, []);
            // not check prefix here, let user ensure this correctness
        } else {
            $prefix = $this->prefix;
        }
        return $this->targetLogDir . '/log' . (empty($this->prefix) ? '' : "-" . $prefix) . $rotateTimeMark . '.log';
    }

    /**
     * @since 2.1
     * Might be used in showing progress
     * Without any DATE or CONTEXT but raw MESSAGE as string, even no tail/lead space
     * @param $message
     */
    public function logInline($message)
    {
        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $message;
            return;
        }
        @file_put_contents($target_file, $message, FILE_APPEND);
    }

    /**
     * If you want to output log directly to STDOUT, use this.
     * @since 2.0 renamed from echo to print
     * @param $level
     * @param $message
     * @param array $context
     * @param bool $enforceEndOfLine @since 2.1
     */
    public function print($level, $message, array $context = array(), $enforceEndOfLine = true)
    {
        if ($this->shouldIgnoreThisLog($level)) {
            return;
        }
        $msg = $this->generateLog($level, $message, $context, $enforceEndOfLine);
        echo $msg;
    }

}