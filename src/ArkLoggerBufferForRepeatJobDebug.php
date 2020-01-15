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
     * ArkLoggerBuffer constructor.
     * @param callable $bufferFlusher
     * @param bool $bufferOnly
     */
    public function __construct($bufferFlusher = null, $bufferOnly = false)
    {
        $this->bufferItems = [];
        $this->bufferOnly = $bufferOnly;
        $this->bufferFlusher = $bufferFlusher;
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

}