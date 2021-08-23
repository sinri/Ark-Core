<?php

namespace sinri\ark\core\exception;

use RuntimeException;
use Throwable;

/**
 * @since 2.7.20
 */
class ArkNestedRuntimeException extends RuntimeException
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

}