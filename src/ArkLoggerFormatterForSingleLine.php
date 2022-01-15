<?php


namespace sinri\ark\core;


class ArkLoggerFormatterForSingleLine extends ArkLoggerAbstractFormatter
{

    /**
     * Return the string format log content.
     * Log content contains three part: context, body and text tail.
     * Context: Timestamp, Process ID, File Path, Level, etc.
     * Body: Message and Object. (as parameters described)
     * Tail: End of line, separator, or empty.
     * @param string $level
     * @param string $message
     * @param array $object
     * @return string
     * @since 2.7.6
     */
    public function generateLog(string $level, string $message, array $object = []): string
    {
        $logHead = $this->getTimeString() . " " . "[$level]";
        $this->lastLogBody = $this->getProcessIDString()
            . $message . " "
            . "|"
            . json_encode($object, JSON_UNESCAPED_UNICODE);

        return $logHead . " " . $this->lastLogBody . $this->getTailString();
    }
}