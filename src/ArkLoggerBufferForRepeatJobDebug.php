<?php


namespace sinri\ark\core;

/**
 * Class ArkLoggerBufferForRepeatJobDebug
 * @package sinri\ark\core
 * @since 2.6
 */
class ArkLoggerBufferForRepeatJobDebug extends ArkLoggerAbstractBuffer
{
    const COMMAND_REPORT_ERROR = "REPORT_ERROR"; // flush+clear
    /**
     * @var ArkLogger
     */
    protected $flushLogger;

    /**
     * ArkLoggerBuffer constructor.9
     * @param callable $bufferFlusher if null, same as use defaultFlusher
     * @param bool $bufferOnly if use tee-like style
     * @param ArkLogger $flushLogger if null use silent logger
     */
    public function __construct($bufferFlusher = null, $bufferOnly = false, $flushLogger = null)
    {
        $this->bufferItems = [];
        $this->bufferOnly = $bufferOnly;

        if ($this->bufferFlusher === null) {
            $bufferFlusher = $this->defaultFlusher();
        }
        $this->bufferFlusher = $bufferFlusher;

        if ($flushLogger === null) {
            $flushLogger = ArkLogger::makeSilentLogger();
        }
        $this->flushLogger = $flushLogger;
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
            default:
                return false;
        }
        return true;
    }

    public function defaultFlusher()
    {
        $this->setBufferFlusher(function ($bufferItems) {
            for ($i = 0; $i < count($bufferItems); $i++) {
                $this->flushLogger->logInline($bufferItems[$i] . PHP_EOL);
            }
            return true;
        });
    }
}