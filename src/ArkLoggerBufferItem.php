<?php


namespace sinri\ark\core;

/**
 * Class ArkLoggerBufferItem
 * @package sinri\ark\core
 * @since 2.3
 */
class ArkLoggerBufferItem
{
    /**
     * @var string
     */
    public $level;
    /**
     * @var int
     */
    public $timestamp;
    /**
     * @var string Y-m-d H:i:s
     */
    public $time;
    /**
     * @var string
     */
    public $content;

    /**
     * ArkLoggerBufferItem constructor.
     * @param $level
     * @param $content
     * @param null|int $timestamp if omitted, use current time
     * @param null|string $time Y-m-d H:i:s, if omitted, use this format follow timestamp
     */
    public function __construct($level, $content, $timestamp = null, $time = null)
    {
        $this->timestamp = ($timestamp === null ? time() : $timestamp);
        $this->time = ($time === null ? date('Y-m-d H:i:s', $this->timestamp) : $time);
        $this->level = $level;
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'level' => $this->level,
            'time' => $this->time,
            'content' => $this->content,
        ];
    }

    /**
     * @return string
     * @since 2.6.1
     */
    public function __toString()
    {
        return $this->time . ' [' . $this->level . '] ' . $this->content;
    }
}