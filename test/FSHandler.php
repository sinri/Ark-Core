<?php


namespace sinri\ark\core\test;


use sinri\ark\core\ArkFSKit;

class FSHandler
{
    public static function test1($item, $dir)
    {
        if (is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
            echo "DIR: $item in $dir" . PHP_EOL;
            ArkFSKit::walkThroughItemsInDir($dir . DIRECTORY_SEPARATOR . $item, [self::class, 'test1']);
        } else {
            echo "FILE: $item in $dir" . PHP_EOL;
        }
    }
}