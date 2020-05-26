<?php


namespace sinri\ark\core;

use Psr\Log\LogLevel;

/**
 * Class ArkLoggerBufferForRepeatJobDebug
 * @package sinri\ark\core
 * @since 2.6
 */
class ArkLoggerBufferForRepeatJobDebug extends ArkLoggerAbstractBuffer
{
    const COMMAND_REPORT_NORMAL = "REPORT_NORMAL"; // only flush higher than ignored level + clear
    const COMMAND_REPORT_ERROR = "REPORT_ERROR"; // flush all + clear
    /**
     * @var ArkLogger
     */
    protected $flushLogger;

    /**
     * @var string values as LogLevel::LEVEL
     */
    protected $ignoreLevel;

    /**
     * ArkLoggerBuffer constructor.9
     * @param callable|null $bufferFlusher if null, same as use defaultFlusher
     * @param bool $bufferOnly if use tee-like style
     * @param ArkLogger|null $flushLogger if null use silent logger
     * @param string $ignoreLevel
     */
    public function __construct($bufferFlusher = null, $bufferOnly = false, $flushLogger = null, $ignoreLevel = LogLevel::INFO)
    {
        $this->bufferItems = [];
        $this->bufferOnly = $bufferOnly;

        if ($this->bufferFlusher === null) {
            $this->useDefaultFlusher();
        } else {
            $this->bufferFlusher = $bufferFlusher;
        }

        if ($flushLogger === null) {
            $flushLogger = ArkLogger::makeSilentLogger();
        }
        $this->flushLogger = $flushLogger;

        $this->ignoreLevel = $ignoreLevel;
    }

    public function appendItem($item)
    {
        $this->bufferItems[] = $item;
    }

    public function whenCommandComesFromLogger($command, $meta = [])
    {
        if (parent::whenCommandComesFromLogger($command, $meta)) return true;

        switch ($command) {
            case self::COMMAND_REPORT_ERROR:
                $this->flush();
                $this->clear();
                break;
            case self::COMMAND_REPORT_NORMAL:
                $this->bufferItems = array_filter($this->bufferItems, function ($item) {
                    return ArkLogger::isLevelSeriousEnough($this->ignoreLevel, $item->level);
                });
                $this->bufferItems = array_values($this->bufferItems);
                $this->flush();
                $this->clear();
                break;
            default:
                return false;
        }
        return true;
    }

    public function useDefaultFlusher()
    {
        $this->setBufferFlusher(function ($bufferItems) {
            for ($i = 0; $i < count($bufferItems); $i++) {
                //if (ArkLogger::isLevelHighEnough($this->flushLogger->getIgnoreLevel(), $bufferItems[$i]->level)) {
                //    $this->flushLogger->logInline($bufferItems[$i] . PHP_EOL);
                //}

                $this->flushLogger->logInline($bufferItems[$i] . PHP_EOL);
            }
            return true;
        });
    }
}