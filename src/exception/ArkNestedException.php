<?php


namespace sinri\ark\core\exception;


use Exception;
use Throwable;

/**
 * Class ArkNestedException
 * @package sinri\ark\core\exception
 * @since 2.7.15
 * @since 2.7.20 Use ArkNestedExceptionTrait
 */
abstract class ArkNestedException extends Exception
{
    use ArkNestedExceptionTrait;

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

//    public function getNestedMessage()
//    {
//        $s = '[' . self::class . '#' . $this->getCode() . '] ' . $this->getMessage();
//        if ($this->getPrevious()) {
//            $s .= PHP_EOL . $this->getPreviousMessages();
//        }
//        $s .= PHP_EOL . 'In ' . $this->getFile() . ':' . $this->getLine();
//        foreach ($this->getTrace() as $item) {
//            $s .= PHP_EOL . self::parseOneTraceItem($item);
//        }
//        return $s;
//    }
//
//    public function getPreviousMessages(): string
//    {
//        $s = '';
//        $previous = $this->getPrevious();
//        if ($previous !== null) {
//            $s .= "↘ " . 'Caused by [' . get_class($previous) . '#' . $previous->getCode() . '] ' . $previous->getMessage();
//
//            if (is_a($previous, ArkNestedException::class)) {
//                $s .= PHP_EOL . $previous->getPreviousMessages();
//            }
//        }
//        return $s;
//    }
//
//    public static function parseOneTraceItem(array $traceItem): string
//    {
//        $x = new ArkArray($traceItem);
//        $s = '↗';
//        if ($x->read(['class'])) {
//            $s .= ' ' . $x->read(['class']) . $x->read(['type']) . $x->read(['function']) . '() ';
//        } elseif ($x->read(['function'])) {
//            $s .= ' ' . $x->read(['function']) . '() ';
//        }
//        $s .= 'In ' . $x->read(['file']) . ':' . $x->read(['line']);
//        return $s;
//    }
}