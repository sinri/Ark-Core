<?php


namespace sinri\ark\core;

/**
 * Class ArkLoggerBuffer
 * @package sinri\ark\core
 * @since 2.3
 */
class ArkLoggerBuffer extends ArkLoggerAbstractBuffer
{
    /**
     * @var int
     */
    protected $bufferSize;

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
     * @param ArkLoggerBufferItem $item
     */
    public function appendItem($item)
    {
        $this->bufferItems[] = $item;
        if (count($this->bufferItems) >= $this->bufferSize) {
            $this->flush();
        }
    }
}