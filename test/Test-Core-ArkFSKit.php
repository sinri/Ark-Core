<?php
require_once __DIR__ . '/../vendor/autoload.php';

\sinri\ark\core\ArkFSKit::walkThroughItemsInDir(__DIR__, [\sinri\ark\core\test\FSHandler::class, 'test1']);

\sinri\ark\core\ArkFSKit::deleteAnything(__DIR__ . '/log/ts');