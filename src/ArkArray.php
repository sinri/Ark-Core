<?php


namespace sinri\ark\core;


use Iterator;

/**
 * Class ArkArray
 * @package sinri\ark\core
 * @since 2.7.1
 * Status: Preview
 */
class ArkArray implements Iterator
{
    /**
     * @var array
     */
    protected $array;

    /**
     * ArkArray constructor.
     * @param array $array The array is a reference, or NULL to automatically create one array since 2.7.11
     */
    public function __construct(&$array = null)
    {
        if ($array === null) {
            $array = [];
        }
        $this->array =& $array;
    }

    /**
     * @return array
     */
    public function getRawArray()
    {
        return $this->array;
    }

    // the Iterator implementations

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->array);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->array);
    }

    public function prev()
    {
        prev($this->array);
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return string|float|int|bool|null scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->array);
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $key = key($this->array);
        return array_key_exists($key, $this->array);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->array);
    }

    // capsuled

    /**
     * @return $this
     */
    public function changeAllKeysToLowerCase()
    {
        $this->array = array_change_key_case($this->array, CASE_LOWER);
        return $this;
    }

    /**
     * @return $this
     */
    public function changeAllKeysToUpperCase()
    {
        $this->array = array_change_key_case($this->array, CASE_UPPER);
        return $this;
    }

    /**
     * @param int $size
     * @param bool $preserve_keys
     * @return array
     */
    public function cutIntoChunks($size, $preserve_keys = false)
    {
        return array_chunk($this->array, $size, $preserve_keys);
    }

    /**
     * @param string|int|null $keyForValue
     * @param null|string|int $keyForKey
     * @return array
     */
    public function fetchColumn($keyForValue, $keyForKey = null)
    {
        return array_column($this->array, $keyForValue, $keyForKey);
    }

    /**
     * Do a Statistic for Value, like
     * select value,count(*) as total from array group by value
     * @return array [value=>total, ...]
     */
    public function getValueStatistics()
    {
        return array_count_values($this->array);
    }

    /**
     * @param array $keys
     * @param mixed $value
     * @return $this
     */
    public function fillForKeys($keys, $value)
    {
        $this->array = array_fill_keys($keys, $value);
        return $this;
    }

    /**
     * @param int $since
     * @param int $total
     * @param mixed $value
     * @return $this
     */
    public function fillForIndexRange($since, $total, $value)
    {
        $this->array = array_fill($since, $total, $value);
        return $this;
    }

    /**
     * @param callable $callable function($v) | function($k) | function($v,$k)
     * @param int $callableParameterFlag 0 (value only) | ARRAY_FILTER_USE_KEY | ARRAY_FILTER_USE_BOTH
     * @return ArkArray
     */
    public function getFilteredCopy($callable, $callableParameterFlag)
    {
        $x = array_filter($this->array, $callable, $callableParameterFlag);
        return new ArkArray($x);
    }

    /**
     * @return ArkArray
     */
    public function getFlippedCopy()
    {
        $x = array_flip($this->array);
        return new ArkArray($x);
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->array);
    }

    /**
     * @param null|mixed $filterByValue
     * @param bool $strict
     * @return array
     */
    public function getKeys($filterByValue = null, $strict = false)
    {
        return array_keys($this->array, $filterByValue, $strict);
    }

    public function containsValue($value, $strict = FALSE)
    {
        return in_array($value, $this->array, $strict);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return array_values($this->array);
    }

    /**
     * @param callable $callable function($arrayItem)
     * @return ArkArray
     */
    public function getCopyByMapping($callable)
    {
        $x = array_map($callable, $this->array);
        return new ArkArray($x);
    }

    /**
     * @param callable $callback function(&$value,$key,$userData=null)
     * @param mixed|null $userData
     * @return $this
     */
    public function walk($callback, $userData = null)
    {
        array_walk($this->array, $callback, $userData);
        return $this;
    }

    /**
     * @param callable $callback function(&$value,$key,$userData=null)
     * @param mixed|null $userData
     * @return $this
     */
    public function walkRecursively($callback, $userData = null)
    {
        array_walk_recursive($this->array, $callback, $userData);
        return $this;
    }

    /**
     * @param int $size
     * @param mixed $value
     * @return $this
     */
    public function pad($size, $value)
    {
        $this->array = array_pad($this->array, $size, $value);
        return $this;
    }

    public function pop()
    {
        return array_pop($this->array);
    }

    /**
     * @param mixed $newTailItem
     * @return $this
     */
    public function push($newTailItem)
    {
        array_push($this->array, $newTailItem);
        return $this;
    }

    public function product()
    {
        return array_product($this->array);
    }

    /**
     * @param int $length
     * @return ArkArray
     */
    public function getRandomPartCopy($length = 1)
    {
        $x = array_rand($this->array, $length);
        return new ArkArray($x);
    }

    /**
     * @param callable $callable function($arrayItem)
     * @param mixed $init
     * @return mixed
     */
    public function reduce($callable, $init = null)
    {
        return array_reduce($this->array, $callable, $init);
    }

    /**
     * @param bool $preserveKeys
     * @return ArkArray
     */
    public function getReversedCopy($preserveKeys = false)
    {
        $x = array_reverse($this->array, $preserveKeys);
        return new ArkArray($x);
    }

    /**
     * @param mixed $item
     * @param bool $strict
     * @return false|int|string
     */
    public function searchItemForKey($item, $strict = false)
    {
        return array_search($item, $this->array, $strict);
    }

    public function shift()
    {
        return array_shift($this->array);
    }

    /**
     * @param mixed $item
     * @return $this
     */
    public function unshift($item)
    {
        array_unshift($this->array, $item);
        return $this;
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     * @return ArkArray
     */
    public function getSliceCopy($offset, $length = null, $preserveKeys = false)
    {
        $x = array_slice($this->array, $offset, $length, $preserveKeys);
        return new ArkArray($x);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @param array|null $replacement
     * @return ArkArray
     */
    public function getSpliceCopy($offset, $length = null, $replacement = null)
    {
        $x = array_splice($this->array, $offset, $length, $replacement);
        return new ArkArray($x);
    }

    public function sum()
    {
        return array_sum($this->array);
    }

    /**
     * @param int $sortFlags SORT_REGULAR | SORT_NUMERIC | SORT_STRING | SORT_LOCALE_STRING
     * @return ArkArray
     */
    public function getUniqueCopy($sortFlags = SORT_STRING)
    {
        $x = array_unique($this->array, $sortFlags);
        return new ArkArray($x);
    }

    public function getCount()
    {
        return count($this->array);
    }

    public function shuffle()
    {
        shuffle($this->array);
        return $this;
    }

    // sort

    /**
     * Sort by value from lowest to highest
     * @param int $sortFlags SORT_REGULAR|SORT_NUMERIC|SORT_STRING|SORT_LOCALE_STRING|SORT_NATURAL|SORT_FLAG_CASE
     * @return $this
     */
    public function sortByValue($sortFlags = SORT_REGULAR)
    {
        sort($this->array, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags SORT_REGULAR|SORT_NUMERIC|SORT_STRING|SORT_LOCALE_STRING|SORT_NATURAL|SORT_FLAG_CASE
     * @return $this
     */
    public function sortByValueReversely($sortFlags = SORT_REGULAR)
    {
        rsort($this->array, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return $this
     */
    public function sortByKey($sortFlags = SORT_REGULAR)
    {
        ksort($this->array, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return $this
     */
    public function sortByKeyReversely($sortFlags = SORT_REGULAR)
    {
        krsort($this->array, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return $this
     */
    public function sortByValueKeepingOriginalKey($sortFlags = SORT_REGULAR)
    {
        asort($this->array, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return $this
     */
    public function sortByValueReverselyKeepingOriginalKey($sortFlags = SORT_REGULAR)
    {
        arsort($this->array, $sortFlags);
        return $this;
    }

    /**
     * @param callable $callable function($value1,$value2):int (i.e. -1,0,1)
     * @return $this
     */
    public function sortByValueWithCallback($callable)
    {
        usort($this->array, $callable);
        return $this;
    }

    /**
     * @param callable $callable function($key1,$key2):int
     * @return $this
     */
    public function sortByKeyWithCallback($callable)
    {
        uksort($this->array, $callable);
        return $this;
    }

    /**
     * @param callable $callback function($value1,$value2):int
     * @return $this
     */
    public function sortByValueWithCallbackKeepingOriginalKey($callback)
    {
        uasort($this->array, $callback);
        return $this;
    }

    // extended

    /**
     * @param string|int|array $keychain
     * @param mixed $default
     * @return mixed
     */
    public function read($keychain, $default = null)
    {
        return ArkHelper::readTarget($this->array, $keychain, $default);
    }

    /**
     * @param string|int|array $keychain $keychain
     * @return bool
     */
    public function validateKeychain($keychain)
    {
        ArkHelper::readTarget($this->array, $keychain, null, null, $exception);
        if ($exception === null) {
            return true;
        }
        return ($exception->getCode() !== ArkHelper::READ_TARGET_FIELD_NOT_FOUND);
    }

    /**
     * @param string|int|array $keychain
     * @param mixed $value
     * @return $this
     */
    public function write($keychain, $value)
    {
        ArkHelper::writeIntoArray($this->array, $keychain, $value);
        return $this;
    }

}