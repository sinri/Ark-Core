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

    /*
     * @since 2.7.1 
     * Changed the log level value according to RFC 5424, Syslog Message Severities
     * Now lower is higher important.
     *
     * In WINNT, Darwin and Linux, the values of PHP defined LOG_* differ.
     * So this class defined them as constants following the DARWIN/LINUX standard.
     */

    const VALUE_OF_EMERGENCY = 0; // LOG_EMERG; // system is unusable; for the situation danger has come
    const VALUE_OF_ALERT = 1; // LOG_ALERT; // action must be taken immediately; for the situation danger is coming
    const VALUE_OF_CRITICAL = 2;//LOG_CRIT; // critical conditions; for the situation risk of danger appeared, should consider upgrade codes to avoid danger
    const VALUE_OF_ERROR = 3;//LOG_ERR; // error conditions; for the runtime errors stopped the codes running
    const VALUE_OF_WARNING = 4;//LOG_WARNING; // warning conditions; for warning when something strange happened
    const VALUE_OF_NOTICE = 5;//LOG_NOTICE; // normal but significant condition; for normal events should be recorded
    const VALUE_OF_INFO = 6;//LOG_INFO; // informational messages; for runtime details, the default level for logging
    const VALUE_OF_DEBUG = 7;//LOG_DEBUG; // debug-level messages; for verbose output to debug
    /**
     * @var ArkLogger
     * @since 2.7.4
     */
    private static $defaultLogger;
    /**
     * @var null|string Give the log storage directory, if null, output to STDOUT
     */
    protected $targetLogDir = null;
    /**
     * @var string
     * A string or an implementation of ArkLoggerPrefixBuilderInterface
     * @since 2.7.13 callable is replaced by ArkLoggerPrefixBuilderInterface
     */
    protected $rawPrefix = '';
    /**
     * @var ArkLoggerPrefixBuilderInterface|null
     */
    protected $prefixBuilder = null;
    /**
     * @var string values as LogLevel::LEVEL
     */
    protected $ignoreLevel;
    /**
     * @var bool if keep completely silent
     */
    protected $silent = false;
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
     * @var ArkLoggerAbstractFormatter
     */
    protected $formatter;

    /**
     * ArkLogger constructor.
     * @param string|null $targetLogDir null for write to STDOUT
     * @param string $prefix
     * @param string|null $rotateTimeFormat string should follow Date Format, and null for no rotating @since 2.2
     * @param null|ArkLoggerAbstractBuffer $buffer if null, buffer off @since 2.3 @since 2.6 switched to ArkLoggerAbstractBuffer
     * @param bool $groupByPrefix If true, the log files with same prefix would be put into a directory named with prefix
     * @param null|ArkLoggerAbstractFormatter $formatter
     */
    public function __construct(
        $targetLogDir = null,
        $prefix = '',
        $rotateTimeFormat = 'Y-m-d',
        $buffer = null,
        $groupByPrefix = false,
        $formatter = null
    )
    {
        $this->targetLogDir = $targetLogDir;
        $this->rawPrefix = $prefix;
        $this->ignoreLevel = LogLevel::INFO;
        $this->rotateTimeFormat = $rotateTimeFormat;
        $this->buffer = $buffer;
        $this->groupByPrefix = $groupByPrefix;

        if ($formatter === null) {
            $this->formatter = new ArkLoggerFormatterForSingleLine();
        } else {
            $this->formatter = $formatter;
        }
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
     * @return ArkLogger
     * @since 2.7.4
     */
    public static function getDefaultLogger(): ArkLogger
    {
        if (self::$defaultLogger === null) {
            self::$defaultLogger = new ArkLogger();
        }
        return self::$defaultLogger;
    }

    /**
     * @param ArkLogger $logger
     * @since 2.7.4
     */
    public static function setDefaultLogger(ArkLogger $logger)
    {
        self::$defaultLogger = $logger;
    }

    /**
     * @return string
     */
    public function getRawPrefix(): string
    {
        return $this->rawPrefix;
    }

    /**
     * @param string $rawPrefix
     * @return ArkLogger
     */
    public function setRawPrefix(string $rawPrefix): ArkLogger
    {
        $this->rawPrefix = $rawPrefix;
        return $this;
    }

    /**
     * @return ArkLoggerPrefixBuilderInterface|null
     */
    public function getPrefixBuilder()
    {
        return $this->prefixBuilder;
    }

    /**
     * @param ArkLoggerPrefixBuilderInterface|null $prefixBuilder
     * @return ArkLogger
     */
    public function setPrefixBuilder($prefixBuilder): ArkLogger
    {
        $this->prefixBuilder = $prefixBuilder;
        return $this;
    }

    /**
     * @param string|ArkLoggerPrefixBuilderInterface $prefix
     * @return ArkLogger
     * @since 2.7.13 callable is replaced by ArkLoggerPrefixBuilderInterface
     * @deprecated use setRawPrefix or setPrefixBuilder instead
     * @since 2.7.14 Fix bug when prefix is not a string
     */
    public function setPrefix($prefix)
    {
        if (is_string($prefix)) {
            $this->setRawPrefix($prefix);
        }
        if (is_a($prefix, ArkLoggerPrefixBuilderInterface::class)) {
            $this->setPrefixBuilder($prefix);
        }

//        if (is_callable($prefix)) {
//            $this->prefix = $prefix;
//        } elseif ($prefix !== '') {
//            $prefix = self::normalizePrefix($prefix,$this->groupByPrefix);
//            // Observed case that forked child process would die here, reason unknown
//        }
//        $this->prefix = $prefix;

        return $this;
    }


    /**
     * @return ArkLoggerAbstractFormatter
     */
    public function getFormatter(): ArkLoggerAbstractFormatter
    {
        return $this->formatter;
    }

    /**
     * @param ArkLoggerAbstractFormatter $formatter
     * @return ArkLogger
     */
    public function setFormatter(ArkLoggerAbstractFormatter $formatter): ArkLogger
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @return string
     */
    public function getIgnoreLevel(): string
    {
        return $this->ignoreLevel;
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
     * @return bool
     * @since 2.7.0
     */
    public function isSilent(): bool
    {
        return $this->silent;
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
     * @deprecated
     */
    public function setShowProcessID(bool $showProcessID)
    {
        $this->formatter->setShowProcessID($showProcessID);
        return $this;
    }

    /**
     * @param null|string $targetLogDir
     * @return ArkLogger
     */
    public function setTargetLogDir($targetLogDir)
    {
        $this->targetLogDir = $targetLogDir;
        return $this;
    }

    /**
     * If you want to output log directly to STDOUT, use this.
     * @param string $level
     * @param string $message
     * @param array $context
     * @return ArkLogger
     * @since 2.0 renamed from echo to print
     * @since 2.7.6 The End of Line Enforcement removed
     */
    public function print($level, $message, array $context = array())
    {
        if ($this->shouldIgnoreThisLog($level)) {
            return $this;
        }

        $msg = $this->formatter->generateLog($level, $message, $context);
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
        return !self::isLevelSeriousEnough($this->ignoreLevel, $level);
    }

    /**
     * @param string $leastSeriousLevel the least serious level which is visible
     * @param string $level
     * @return bool
     * @since 2.6.3
     */
    public static function isLevelSeriousEnough($leastSeriousLevel, $level)
    {
        static $levelValue = [
            LogLevel::EMERGENCY => self::VALUE_OF_EMERGENCY,
            LogLevel::ALERT => self::VALUE_OF_ALERT,
            LogLevel::CRITICAL => self::VALUE_OF_CRITICAL,
            LogLevel::ERROR => self::VALUE_OF_ERROR,
            LogLevel::WARNING => self::VALUE_OF_WARNING,
            LogLevel::NOTICE => self::VALUE_OF_NOTICE,
            LogLevel::INFO => self::VALUE_OF_INFO,
            LogLevel::DEBUG => self::VALUE_OF_DEBUG,
        ];
        $coming = ArkHelper::readTarget($levelValue, $level, self::VALUE_OF_INFO);
        $limit = ArkHelper::readTarget($levelValue, $leastSeriousLevel, self::VALUE_OF_DEBUG);
        return ($coming <= $limit);
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

//    /**
//     * Return the string format log content
//     * @param $level
//     * @param $message
//     * @param string|array $object
//     * @param bool $enforceEndOfLine @since 2.1
//     * @param string $logBody @since 2.3
//     * @return string
//     * @deprecated use formatter
//     */
//    protected function generateLog($level, $message, $object = '', $enforceEndOfLine = true, &$logBody = "")
//    {
//        $now = date('Y-m-d H:i:s');
//        $level_string = "[{$level}]";
//        $logHead = "{$now} {$level_string}";
//
//        $logBody = "";
//        if ($this->showProcessID) {
//            $logBody .= "PID: " . getmypid() . " ";
//        }
//        $logBody .= "{$message} |" . (is_string($object) ? $object : json_encode($object, JSON_UNESCAPED_UNICODE));
//
//        return $logHead . " " . $logBody . ($enforceEndOfLine ? PHP_EOL : "");
//    }

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
        $msg = $this->formatter->generateLog($level, $message, $context);

        if ($this->buffer !== null) {
            $this->buffer->appendRaw($level, $this->formatter->getLastLogBody());
            if ($this->buffer->isBufferOnly()) {
                return $this;
            }
        }

        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $msg;
        } else {
            @file_put_contents($target_file, $msg, FILE_APPEND);
        }
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
     * @return string|false it returns FALSE when the logger has to output to STDOUT directly
     * @since 2.2
     * @since 2.5 Add group by prefix support
     * @since 2.7.13 callable is replaced by ArkLoggerPrefixBuilderInterface
     * @since 2.7.13 nested directory is allowed
     */
    public function getCurrentLogFilePath()
    {
        if (empty($this->targetLogDir)) {
            return false;
        }

        $rotateTimeMark = "";
        if ($this->rotateTimeFormat !== null) {
            $rotateTimeMark .= "-" . date($this->rotateTimeFormat);
        }

        if ($this->prefixBuilder !== null) {
            // as ArkLoggerPrefixBuilderInterface
            $prefix = self::normalizePrefix($this->prefixBuilder->buildPrefix(), $this->groupByPrefix);
        } else {
            $prefix = self::normalizePrefix($this->rawPrefix, $this->groupByPrefix);
        }

//        if (is_callable($this->prefix)) {
//            // Notice when developing 2.7.13:
//            // We used `_` to replace all illegal characters, bu a single `_` is an alias of function `gettext`.
//            // see https://www.php.net/manual/en/function.gettext.php
//
//            $prefix = call_user_func_array($this->prefix, []);
//            // [del]not check prefix here, let user ensure this correctness[/del]
//            $prefix = self::normalizePrefix($prefix,$this->groupByPrefix);
//            // I thought again and add this check...
//        } else {
//            $prefix = $this->prefix;
//        }

        $dir = $this->targetLogDir;
        $file = 'log' . (empty($prefix) ? '' : "-" . $prefix) . $rotateTimeMark . '.log';

        if ($this->groupByPrefix) {
            if ($prefix === '') {
                $dir = $this->targetLogDir . DIRECTORY_SEPARATOR . 'default-log';
                $file = 'log' . $rotateTimeMark . '.log';
            } else {
                // Since 2.7.13
                // before: prefix a/b/c -> DIR/a/b/c/log-c-YMD.log
                // as of: prefix a/b/c -> DIR/a_b_c/log-a_b_c-YMD.log

                $components = explode('/', $prefix);
                $components = array_filter($components);
                if (count($components) === 1) {
                    $prefix = $components[0];
                    $dir = $this->targetLogDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $components);
                    $file = 'log' . "-" . $prefix . $rotateTimeMark . '.log';
                } elseif (count($components) > 1) {
                    $pathComponents = array_slice($components, 0, -1);
                    $prefixComponents = array_slice($components, -1, 1);
                    $dir = $this->targetLogDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathComponents);
                    $file = 'log' . "-" . $prefixComponents[0] . $rotateTimeMark . '.log';
                } else {
                    $dir = $this->targetLogDir . DIRECTORY_SEPARATOR . 'default-log';
                    $file = 'log' . $rotateTimeMark . '.log';
                }
            }
        }

        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }

        return $dir . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * @param string $rawPrefix
     * @param bool $groupByPrefix
     * @return array|string|string[]|null
     * @since 2.7.13 add group by prefix switch
     */
    public static function normalizePrefix(string $rawPrefix, bool $groupByPrefix)
    {
        // Since 2.7.13 let `/` be kept when group prefix function is enabled
        if ($groupByPrefix) {
            return preg_replace('/[^A-Za-z0-9\/]/', '_', $rawPrefix);
        }
        return preg_replace('/[^A-Za-z0-9]/', '_', $rawPrefix);
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
     * A special NOTICE level log for call stack
     * @return $this
     */
    public function signpost()
    {
        $this->notice(ArkHelper::getDebugBacktraceString());
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

    /**
     * @param int $errNo
     * @param string $errStr
     * @param string $errFile
     * @param int $errLine
     * @return ArkLogger
     * @since 2.7.2
     * @since 2.7.7 Add Context Parameter of record process info to the debug log
     */
    public function logErrorInHandler(int $errNo, string $errStr, string $errFile, int $errLine)
    {
        $systemErrorTypeName = 'UnknownError';
        $systemErrorExpression = $errFile . '@' . $errLine . ' ' . $errStr;
        $context = ['record_pid' => getmypid(), 'record_uid' => getmyuid()];
        switch ($errNo) {
            case E_ERROR:
                $systemErrorTypeName = 'E_ERROR';
                $this->error($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_USER_ERROR:
                $systemErrorTypeName = 'E_USER_ERROR';
                $this->error($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_WARNING:
                $systemErrorTypeName = 'E_WARNING';
                $this->warning($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_USER_WARNING:
                $systemErrorTypeName = 'E_USER_WARNING';
                $this->warning($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_NOTICE:
                $systemErrorTypeName = 'E_NOTICE';
                $this->notice($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_USER_NOTICE:
                $systemErrorTypeName = 'E_USER_NOTICE';
                $this->notice($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_STRICT:
                $systemErrorTypeName = 'E_STRICT';
                $this->notice($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_DEPRECATED:
                $systemErrorTypeName = 'E_DEPRECATED';
                $this->notice($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            case E_USER_DEPRECATED:
                $systemErrorTypeName = 'E_USER_DEPRECATED';
                $this->notice($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
            default:
                $this->error($systemErrorTypeName . ' ' . $systemErrorExpression, $context);
                break;
        }
        $this->logInline(ArkHelper::getDebugBacktraceString() . PHP_EOL);
        return $this;
    }

    /**
     * Directly output string to target
     * @param string $message
     * @return ArkLogger
     * @since 2.1
     * Might be used in showing progress
     * Without any DATE or CONTEXT but raw MESSAGE as string, even no tail/lead space
     */
    public function logInline(string $message)
    {
        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $message;
            return $this;
        }
        @file_put_contents($target_file, $message, FILE_APPEND);
        return $this;
    }
}