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
     * Use callback to process any child in the given dir
     * @param string $dir
     * @param callable $callback such as function(string item,string dir): void
     * @since 2.5
     */
    public static function walkThroughItemsInDir($dir, $callback)
    {
        $handle = opendir($dir);
        while (($item = readdir($handle)) !== false) {
            if ($item === '.' || $item === '..') continue;
            call_user_func_array($callback, [$item, $dir]);
        }
        closedir($handle);
    }

    /**
     * Try to delete directory or file completely...
     * But, you may fail if exception occurs, such as privileges lack or so
     * @param string $path
     * @since 2.5
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

    /**
     * To create a directory for given file (as its parent component)
     * @param string $filePath path to file, such as /path/to/file.extension
     * @param int $mode
     * @return bool
     * @since 2.5
     */
    public static function ensureDirectoryForFilePath($filePath, $mode = 0777)
    {
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);
        return mkdir($dir, $mode, true);
    }
}