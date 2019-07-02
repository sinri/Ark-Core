<?php


namespace sinri\ark\core;

/**
 * Class ArkLoggerBuffer
 * @package sinri\ark\core
 * @since 2.3
 */
class ArkLoggerBuffer
{
    /**
     * @var int
     */
    protected $bufferSize;
    /**
     * @var ArkLoggerBufferItem[]
     */
    protected $bufferItems;
    /**
     * @var callable a function to use the full buffer, and the buffer would be cleared when ti return true.
     *  e.g. function(ArkLoggerBufferItem[] $items):bool
     */
    protected $bufferFlusher;
    /**
     * If this is true, log should only be written to buffer while not to common output file or so
     * @var bool
     */
    protected $bufferOnly;

    /**
     * ArkLoggerBuffer constructor.
     * @param int $bufferSize
     * @param callable $bufferFlusher
     * @param bool $bufferOnly
     */
    public function __construct($bufferSize = 100, $bufferFlusher = null, $bufferOnly = false)
    {
        $this->bufferItems = [];
        $this->bufferSize = $bufferSize;
        $this->bufferOnly = $bufferOnly;
        $this->bufferFlusher = $bufferFlusher;
    }

    /**
     * @return int
     */
    public function getBufferSize(): int
    {
        return $this->bufferSize;
    }

    /**
     * @param int $bufferSize
     */
    public function setBufferSize(int $bufferSize)
    {
        $this->bufferSize = $bufferSize;
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
     */
    public function setBufferFlusher(callable $bufferFlusher)
    {
        $this->bufferFlusher = $bufferFlusher;
    }

    /**
     * @return bool
     */
    public function isBufferOnly(): bool
    {
        return $this->bufferOnly;
    }

    /**
     * @param bool $bufferOnly
     */
    public function setBufferOnly(bool $bufferOnly)
    {
        $this->bufferOnly = $bufferOnly;
    }

    /**
     * @param string $level
     * @param string $content
     * @param string $time Y-m-d H:i:s
     */
    public function appendRaw($level, $content, $time = null)
    {
        $this->appendItem(new ArkLoggerBufferItem($level, $content, $time));
    }

    /**
     * @param ArkLoggerBufferItem $item
     */
    public function appendItem($item)
    {
        $this->bufferItems[] = $item;
        if (count($this->bufferItems) >= $this->bufferSize) {
            $this->flush();
        }
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
}