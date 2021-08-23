<?php

namespace sinri\ark\core\exception;

use Exception;
use RuntimeException;
use sinri\ark\core\ArkArray;

/**
 * @since 2.7.20
 */
trait ArkNestedExceptionTrait
{

    public function getNestedMessage()
    {
        $that = $this->_traitThisAsException();

        $s = '[' . self::class . '#' . $that->getCode() . '] ' . $that->getMessage();
        if ($that->getPrevious()) {
            $s .= PHP_EOL . $this->getPreviousMessages();
        }
        $s .= PHP_EOL . 'In ' . $that->getFile() . ':' . $that->getLine();
        foreach ($that->getTrace() as $item) {
            $s .= PHP_EOL . self::parseOneTraceItem($item);
        }
        return $s;
    }

    protected function _traitThisAsException(): Exception
    {
        if (is_a($this, Exception::class)) {
            return $this;
        }
        throw new RuntimeException("The class to use trait is not Exception.");
    }

    public function getPreviousMessages(): string
    {
        $that = $this->_traitThisAsException();

        $s = '';
        $previous = $that->getPrevious();
        if ($previous !== null) {
            $s .= "↘ " . 'Caused by [' . get_class($previous) . '#' . $previous->getCode() . '] ' . $previous->getMessage();

            if (is_a($previous, ArkNestedException::class)) {
                $s .= PHP_EOL . $previous->getPreviousMessages();
            }
        }
        return $s;
    }

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