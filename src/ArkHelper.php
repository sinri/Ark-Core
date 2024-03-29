<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/9/7
 * Time: 00:14
 */

namespace sinri\ark\core;

use sinri\ark\core\exception\EnsureItemException;
use sinri\ark\core\exception\LookUpTargetException;

class ArkHelper
{
    /**
     * Follow PSR-0/PSR-4, implementation comes from sinri\enoch
     * @param string $class_name such as sinri\enoch\test\routing\controller\SampleHandler
     * @param string $base_namespace such as sinri\enoch
     * @param string $base_path /code/sinri/enoch
     * @param string $extension
     * @return null|string
     */
    public static function getFilePathForClassName($class_name, $base_namespace, $base_path, $extension = '.php')
    {
        if (strpos($class_name, $base_namespace) === 0) {
            $class_file = str_replace($base_namespace, $base_path, $class_name);
            $class_file .= $extension;
            return str_replace('\\', '/', $class_file);
        }
        return null;
    }

    /**
     * For Autoload File
     * Notice: It is better to use the autoload function provided by Composer.
     * @param string $base_namespace such as sinri\enoch
     * @param string $base_path /code/sinri/enoch
     * @param string $extension
     */
    public static function registerAutoload($base_namespace, $base_path, $extension = '.php')
    {
        spl_autoload_register(function ($class_name) use ($base_namespace, $base_path, $extension) {
            $file_path = self::getFilePathForClassName(
                $class_name,
                $base_namespace,
                $base_path,
                $extension
            );
            if ($file_path) {
                require_once $file_path;
            }
        });
    }

    const READ_TARGET_NO_ERROR = 0;
    const READ_TARGET_FIELD_NOT_FOUND = 1;
    const READ_TARGET_REGEX_NOT_MATCH = 2;
    const READ_TARGET_SOURCE_ERROR = 3;

    /**
     * @param object|array $target
     * @param string|int|array $keychain
     * @param mixed $default
     * @param null|String $regex
     * @param null|LookUpTargetException $exception
     * @return mixed
     */
    public static function readTarget($target, $keychain, $default = null, $regex = null, &$exception = null)
    {
        if (is_array($target)) {
            if (is_array($keychain)) {
                $headKey = array_shift($keychain);
                if (empty($keychain)) {
                    return self::readTarget($target, $headKey, $default, $regex, $exception);
                }
                $sub_array = self::readTarget($target, $headKey, [], null, $exception);
                return self::readTarget($sub_array, $keychain, $default, $regex, $exception);
            } else {
                if (key_exists($keychain, $target)) {
                    $value = $target[$keychain];
                    if ($regex !== null && !preg_match($regex, $value)) {
                        $exception = new LookUpTargetException("REGEX_NOT_MATCH", self::READ_TARGET_REGEX_NOT_MATCH);
                        return $default;
                    }
                    $exception = null;
                    return $value;
                } else {
                    $exception = new LookUpTargetException("FIELD_NOT_FOUND", self::READ_TARGET_FIELD_NOT_FOUND);
                    return $default;
                }
            }
        } elseif (is_object($target)) {
            if (is_array($keychain)) {
                $headKey = array_shift($keychain);
                if (empty($keychain)) {
                    return self::readTarget($target, $headKey, $default, $regex, $exception);
                }
                $sub_array = self::readTarget($target, $headKey, [], null, $exception);
                return self::readTarget($sub_array, $keychain, $default, $regex, $exception);
            } else {
                if (property_exists($target, $keychain)) {
                    $value = $target->$keychain;
                    if ($regex !== null && !preg_match($regex, $value)) {
                        $exception = new LookUpTargetException("REGEX_NOT_MATCH", self::READ_TARGET_REGEX_NOT_MATCH);
                        return $default;
                    }
                    $exception = null;
                    return $value;
                } else {
                    $exception = new LookUpTargetException("FIELD_NOT_FOUND", self::READ_TARGET_FIELD_NOT_FOUND);
                    return $default;
                }
            }
        } else {
            // not array nor object
            $exception = new LookUpTargetException("SOURCE_ERROR", self::READ_TARGET_SOURCE_ERROR);
            return $default;
        }
    }

    /**
     * @param object|array $target
     * @param string|int|array $keychain
     * @return int
     * @throws LookUpTargetException
     * @since 2.7.17
     * @since 2.7.19 Fix 2.7.17
     */
    public static function readTargetForInteger($target, $keychain): int
    {
        $x = self::readTarget($target, $keychain);
        if (!is_numeric($x) || preg_match('/^[+-]?\d+$/', $x) !== 1) {
            throw new LookUpTargetException("Cannot read integer from target with keychain");
        }
        return intval($x);
    }

    /**
     * @param object|array $target
     * @param string|int|array $keychain
     * @return float
     * @throws LookUpTargetException
     * @since 2.7.17
     */
    public static function readTargetForFloat($target, $keychain): float
    {
        $x = self::readTarget($target, $keychain);
        if (!is_numeric($x)) {
            throw new LookUpTargetException("Cannot read float from target with keychain");
        }
        return floatval($x);
    }

    /**
     * @param array $array
     * @param array|string|int $keychain
     * @param mixed $value
     */
    public static function writeIntoArray(&$array, $keychain, $value)
    {
        if (!is_array($array)) {
            $array = [];
        }
        if (!is_array($keychain)) {
            $keychain = [$keychain];
        }

        $headKey = array_shift($keychain);
        if (empty($keychain)) {
            //last
            $array[$headKey] = $value;
        } else {
            //not last
            if (!isset($array[$headKey])) {
                $array[$headKey] = [];
            }
            self::writeIntoArray($array[$headKey], $keychain, $value);
        }
    }

    /**
     * @param object $object
     * @param array|string $keychain
     * @param mixed $value
     * @since 2.7.1
     */
    public static function writeIntoObject(&$object, $keychain, $value)
    {
        if (!is_object($object)) {
            $object = (object)array();//json_decode(json_encode([]));
        }
        if (!is_array($keychain)) {
            $keychain = [$keychain];
        }

        $headKey = array_shift($keychain);
        if (empty($keychain)) {
            // last
            $object->$headKey = $value;
        } else {
            // not last
            if (!isset($object->$headKey)) {
                $object->$headKey = (object)array();//json_decode(json_encode([]));
            }
            if (is_array($object->$headKey)) {
                self::writeIntoArray($object->$headKey, $keychain, $value);
            } else {
                self::writeIntoObject($object->$headKey, $keychain, $value);
            }
        }
    }

    /**
     * Unset item and nested item in array
     * @param array $array
     * @param array|string|int $keychain
     * @since 1.2
     */
    public static function removeFromArray(&$array, $keychain)
    {
        if (!is_array($array)) {
            $array = [];
        }
        if (!is_array($keychain)) {
            $keychain = [$keychain];
        }

        $headKey = array_shift($keychain);
        if (empty($keychain)) {
            //last
            unset($array[$headKey]);
        } else {
            //not last
            if (isset($array[$headKey])) {
                self::removeFromArray($array[$headKey], $keychain);
            }
        }
    }

    const ASSERT_TYPE_NOT_EMPTY = 0b111;
    const ASSERT_TYPE_NOT_VAIN = 0b1;
    const ASSERT_TYPE_NOT_NULL = 0b10;
    const ASSERT_TYPE_NOT_FALSE = 0b100;

    /**
     * @param mixed $object
     * @param string $exception_message
     * @param int $type
     * @throws EnsureItemException
     */
    public static function assertItem($object, $exception_message = null, $type = self::ASSERT_TYPE_NOT_EMPTY)
    {
        if ($exception_message === null) {
            $exception_message = __FUNCTION__;
        }
        if (($type & 0b100) > 0 && $object === false) {
            throw new EnsureItemException($exception_message);
        }
        if (($type & 0b10) > 0 && $object === null) {
            throw new EnsureItemException($exception_message);
        }
        if (($type & 0b1) > 0 && empty($object)) {
            throw new EnsureItemException($exception_message);
        }
    }

    /**
     * @param string $error
     * @param mixed ...$parameters
     * @throws EnsureItemException
     */
    public static function quickNotEmptyAssert($error, ...$parameters)
    {
        foreach ($parameters as $parameter) {
            self::assertItem($parameter, $error);
        }
    }

    /**
     * @param array[] $list
     * @param string $keyField
     * @return array
     */
    public static function turnListToMapping($list, $keyField)
    {
        if (empty($list) || !is_array($list)) {
            return [];
        }
        $map = [];
        foreach ($list as $key => $item) {
            if (!isset($item[$keyField])) {
                $map[$key] = $item;
            } else {
                $map[$item[$keyField]] = $item;
            }
        }
        return $map;
    }

    /**
     * @return bool
     */
    public static function isCLI()
    {
        return php_sapi_name() === 'cli';
    }

    // For more @see https://www.php.net/manual/en/timezones.php
    const TIMEZONE_SHANGHAI = "Asia/Shanghai"; // +8
    const TIMEZONE_TOKYO = "Asia/Tokyo"; // +9

    /**
     * Set Timezone as +08:00 Shanghai, P. R. China
     * @param string $timezoneID
     * @since 2.6.8
     */
    public static function configureTimezone($timezoneID = self::TIMEZONE_SHANGHAI)
    {
        date_default_timezone_set($timezoneID);
    }

    /**
     * @param string $string
     * @param string $prefix
     * @param bool $caseInsensitive
     * @return bool
     * @since 2.7.0
     */
    public static function stringHasPrefix($string, $prefix, $caseInsensitive = false)
    {
        if ($caseInsensitive) {
            return stripos($string, $prefix) === 0;
        } else {
            return strpos($string, $prefix) === 0;
        }
    }

    /**
     * @param string $string
     * @param string $subString
     * @param bool $caseInsensitive
     * @return bool
     * @since 2.7.0
     */
    public static function stringContainsSubString($string, $subString, $caseInsensitive = false)
    {
        if ($caseInsensitive) {
            return stripos($string, $subString) !== false;
        } else {
            return strpos($string, $subString) !== false;
        }
    }

    /**
     * @param string $string
     * @param string $suffix
     * @param bool $caseInsensitive
     * @return bool
     * @since 2.7.0
     */
    public static function stringHasSuffix($string, $suffix, $caseInsensitive = false)
    {
        if ($caseInsensitive) {
            return stripos($string, $suffix) === (strlen($string) - strlen($suffix));
        } else {
            return strpos($string, $suffix) === (strlen($string) - strlen($suffix));
        }
    }

    /**
     * @return array
     * @since 2.7.1
     */
    public static function getDebugBacktrace()
    {
        return debug_backtrace();
    }

    /**
     * @return string
     * @since 2.7.1
     */
    public static function getDebugBacktraceString()
    {
        $debug = debug_backtrace();
        $string = "";
        foreach ($debug as $index => $item) {
            if ($index === 0) {
                $string .= "[$index] Called by " . PHP_EOL;
            } else {
                $string .= "[$index] Which is called by " . PHP_EOL;
            }
            //$string .= "Called by ". $index.' th caller'.PHP_EOL;
            $string .= "\tLocation: " . ArkHelper::readTarget($item, ['file'], '?') . '@' . ArkHelper::readTarget($item, ['line'], '?') . PHP_EOL;
            $string .= "\tMethod: " . ArkHelper::readTarget($item, ['class'], '?') . ArkHelper::readTarget($item, ['type'], '?') . ArkHelper::readTarget($item, ['function'], '?') . PHP_EOL;
            if (isset($item['args']) && !empty($item['args'])) {
                $string .= "\tArguments: ";
                //. implode(',', $item['args']) . PHP_EOL;
                $argsMapped = array_map('json_encode', $item['args']);
                $string .= implode(', ', $argsMapped) . PHP_EOL;
            }
//            if(array_key_exists('object',$item)){
//                $string .= "\tEntity: ".var_export($item['object'],true).PHP_EOL;
//            }
        }
        return $string;
    }

    /**
     * It is a complete implement, while you can also take it as sample to build thy own
     * @param ArkLogger $logger
     * @param int $level
     * @param bool $takeErrorAsFixed 如果函数返回 FALSE，标准错误处理处理程序将会继续调用。
     * @param bool $showBacktrace @since 2.7.23 add this
     * @return callable|null
     * @since 2.7.2
     * @since 2.7.16 Make it more friendly for ArkCache using FILE SYSTEM
     * @since 2.7.18 Fix bug in 2.7.16 (mistake $errStr for $errFile)
     */
    public static function registerErrorHandlerForLogging(ArkLogger $logger, $level = E_ALL | E_STRICT, bool $takeErrorAsFixed = false, bool $showBacktrace = true)
    {
        return set_error_handler(
            function (int $errNo, string $errStr, string $errFile, int $errLine) use ($showBacktrace, $takeErrorAsFixed, $logger) {
                // @since 2.7.22
                // https://www.php.net/manual/en/language.operators.errorcontrol.php
                // If a custom error handler function is set with set_error_handler(),
                // it will still be called even though the diagnostic has been suppressed,
                // as such the custom error handler should call error_reporting()
                // and verify that the @ operator was used.
                // Warning
                // Prior to PHP 8.0.0, the value of the severity passed to the custom error handler was always 0 if the diagnostic was suppressed.
                // This is no longer the case as of PHP 8.0.0.
                if (!(error_reporting() & $errNo)) {
                    return false; // Silenced
                }

                $logger->logErrorInHandler($errNo, $errStr, $errFile, $errLine, $showBacktrace);
                return $takeErrorAsFixed;
            },
            $level
        );
    }

    /**
     * Just restore the error handler as PHP provided power
     * @since 2.7.2
     */
    public static function restoreErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * @param object $object
     * @param string $trait TRAIT::class
     * @return bool
     * @since 2.7.21
     */
    public static function usingTrait($object, $trait)
    {
        if (!is_object($object)) return false;
        $x = class_uses(get_class($object));
        return in_array($trait, $x);
    }
}