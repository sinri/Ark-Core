<?php


namespace sinri\ark\core;

/**
 * Class ArkLoggerAbstractBuffer
 * @package sinri\ark\core
 * @since 2.6
 */
abstract class ArkLoggerAbstractBuffer
{
    const COMMAND_FLUSH = "FLUSH";
    const COMMAND_CLEAR = "CLEAR";

    /**
     * @var ArkLoggerBufferItem[]
     */
    protected $bufferItems;
    /**
     * @var callable a function to use the full buffer, and the buffer would be cleared when it return true.
     *  e.g. function(ArkLoggerBufferItem[] $items):bool
     */
    protected $bufferFlusher;
    /**
     * If this is true, log should only be written to buffer while not to common output file or so
     * @var bool
     */
    protected $bufferOnly;

    /**
     * @return bool
     */
    public function isBufferOnly(): bool
    {
        return $this->bufferOnly;
    }

    /**
     * @param bool $bufferOnly
     * @return ArkLoggerAbstractBuffer
     */
    public function setBufferOnly(bool $bufferOnly): ArkLoggerAbstractBuffer
    {
        $this->bufferOnly = $bufferOnly;
        return $this;
    }

    /**
     * @return callable
     */
    public function getBufferFlusher(): callable
    {
        return $this->bufferFlusher;
    }

    /**
     * @param callable $bufferFlusher
     * @return ArkLoggerAbstractBuffer
     */
    public function setBufferFlusher(callable $bufferFlusher): ArkLoggerAbstractBuffer
    {
        $this->bufferFlusher = $bufferFlusher;
        return $this;
    }

    /**
     * @param $item
     * @return ArkLoggerBufferItem
     */
    abstract public function appendItem($item);

    /**
     * It is a shortcut
     * @param string $level
     * @param string $content
     * @param string $time Y-m-d H:i:s
     */
    public final function appendRaw($level, $content, $time = null)
    {
        $this->appendItem(new ArkLoggerBufferItem($level, $content, $time));
    }

    public function flush()
    {
        if (call_user_func_array($this->bufferFlusher, [$this->bufferItems])) {
            $this->clear();
            return true;
        }
        return false;
    }

    public function clear()
    {
        $this->bufferItems = [];
    }

    public function size()
    {
        return count($this->bufferItems);
    }

    /**
     * @param string $command
     * @param array $meta
     * @return bool If the command is handled
     */
    public function whenCommandComesFromLogger($command, $meta = [])
    {
        switch ($command) {
            case self::COMMAND_CLEAR:
                $this->clear();
                break;
            case self::COMMAND_FLUSH:
                $this->flush();
                break;
            default:
                // here false means command is not handled
                return false;
        }
        return true;
    }
}