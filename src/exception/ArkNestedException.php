<?php


namespace sinri\ark\core\exception;


use Exception;
use sinri\ark\core\ArkArray;
use Throwable;

/**
 * Class ArkNestedException
 * @package sinri\ark\core\exception
 * @since 2.7.15
 */
abstract class ArkNestedException extends Exception
{
    /**
     * OctetDatabaseQueryException constructor.
     * @param Throwable|null $previous
     * @param string $message
     * @param int|null $code
     */
    public function __construct(Throwable $previous = null, $message = "", $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getNestedMessage()
    {
        $s = '[' . self::class . '#' . $this->getCode() . '] ' . $this->getMessage();
        if ($this->getPrevious()) {
            $s .= PHP_EOL . $this->getPreviousMessages();
        }
        $s .= PHP_EOL . 'In ' . $this->getFile() . ':' . $this->getLine();
        foreach ($this->getTrace() as $item) {
            $s .= PHP_EOL . self::parseOneTraceItem($item);
        }
        return $s;
    }

    public function getPreviousMessages(): string
    {
        $s = '';
        $previous = $this->getPrevious();
        if ($previous !== null) {
            $s .= "↘ " . 'Caused by [' . get_class($previous) . '#' . $previous->getCode() . '] ' . $previous->getMessage();

            if (is_a($previous, ArkNestedException::class)) {
                $s .= PHP_EOL . $previous->getPreviousMessages();
            }
        }
        return $s;
    }

//    /**
//     * @param Throwable|null $previous
//     * @return string
//     */
//    protected static function parsePreviousToMessage(Throwable $previous): string
//    {
//        if ($previous === null) return '';
//        $s = "\t" . 'Caused by ';
//        $s .= '[' . get_class($previous) . '] ' . $previous->getMessage() . PHP_EOL;
//        $s .= "\tTrace: ";// . $previous->getTraceAsString();
//
//        $list = preg_split('/[\r\n]+/', $previous->getTraceAsString());
//        foreach ($list as $item) {
//            $s .= "\t\t" . $item . PHP_EOL;
//        }
//        return $s;
//    }

    public static function parseOneTraceItem(array $traceItem): string
    {
        $x = new ArkArray($traceItem);
        $s = '↗';
        if ($x->read(['class'])) {
            $s .= ' ' . $x->read(['class']) . $x->read(['type']) . $x->read(['function']) . '() ';
        } elseif ($x->read(['function'])) {
            $s .= ' ' . $x->read(['function']) . '() ';
        }
        $s .= 'In ' . $x->read(['file']) . ':' . $x->read(['line']);
        return $s;
    }
}