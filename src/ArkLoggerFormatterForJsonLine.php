<?php


namespace sinri\ark\core;


class ArkLoggerFormatterForJsonLine extends ArkLoggerAbstractFormatter
{

    /**
     * Return the string format log content
     * Log content contains three part: context, body and text tail.
     * Context: Timestamp, Process ID, File Path, Level, etc.
     * Body: Message and Object. (as parameters described)
     * Tail: End of line, separator, or empty.
     * @param string $level
     * @param string $message
     * @param array $object
     * @return string
     * @since 2.7.5
     */
    public function generateLog(string $level, string $message, array $object = []): string
    {
        $this->lastLogBody = json_encode(
            [
                'time' => $this->getTimeString(),
                'level' => $level,
                'message' => $message,
                'object' => $object,
            ],
            JSON_UNESCAPED_UNICODE
        );
        return $this->lastLogBody . $this->getTailString();
    }
}