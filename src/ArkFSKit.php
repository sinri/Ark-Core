<?php


namespace sinri\ark\core;

/**
 * Class ArkFSKit
 * @package sinri\ark\core
 * @since 2.5
 */
class ArkFSKit
{
    /**
     * @param string $dir
     * @param callable $callback such as function(string item,string dir): void
     */
    public static function walkThroughItemsInDir($dir, $callback)
    {
        $handle = opendir($dir);
        while (($item = readdir($handle)) !== false) {
            if ($item === '.' || $item === '..') continue;
            call_user_func_array($callback, [$item, $dir]);
        }
    }

    /**
     * @param string $path
     */
    public static function deleteAnything($path)
    {
        if (!file_exists($path)) {
            return;
        }
        if (is_file($path)) {
            unlink($path);
            return;
        }
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if (($file != '.') && ($file != '..')) {
                $full = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($full)) {
                    self::deleteAnything($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($handle);
        rmdir($path);
    }
}