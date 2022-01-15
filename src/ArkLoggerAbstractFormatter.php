<?php


namespace sinri\ark\core;


abstract class ArkLoggerAbstractFormatter
{
    /**
     * @var bool If show PID within every log row
     */
    protected $showProcessID = false;
    /**
     * @var string
     */
    protected $tail = PHP_EOL;
    /**
     * @var string
     */
    protected $lastLogBody = '';

    /**
     * @param string $tail
     * @return ArkLoggerAbstractFormatter
     */
    public function setTail(string $tail): ArkLoggerAbstractFormatter
    {
        $this->tail = $tail;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowProcessID(): bool
    {
        return $this->showProcessID;
    }

    /**
     * @param bool $showProcessID
     * @return ArkLoggerAbstractFormatter
     */
    public function setShowProcessID(bool $showProcessID): ArkLoggerAbstractFormatter
    {
        $this->showProcessID = $showProcessID;
        return $this;
    }

    public function getTimeString()
    {
        return date('Y-m-d H:i:s');
    }

    public function getProcessIDString()
    {
        if ($this->showProcessID) {
            return "PID: " . getmypid() . " ";
        }
        return "";
    }

    public function getTailString()
    {
        return $this->tail;
    }

    /**
     * @return string
     */
    public function getLastLogBody(): string
    {
        return $this->lastLogBody;
    }

    /**
     * Return the string format log content.
     * Log content contains three part: context, body and text tail.
     * Context: Timestamp, Process ID, File Path, etc.
     * Body: Level, Message, Object. (as parameters described)
     * Tail: End of line, separator, or empty.
     * @param string $level
     * @param string $message
     * @param array $object
     * @return string
     * @since 2.7.6
     */
    abstract public function generateLog(string $level, string $message, array $object = []): string;
}