<?php

use sinri\ark\core\ArkFSKit;
use sinri\ark\core\test\FSHandler;

require_once __DIR__ . '/../vendor/autoload.php';

ArkFSKit::walkThroughItemsInDir(__DIR__, [FSHandler::class, 'test1']);

ArkFSKit::deleteAnything(__DIR__ . '/log/ts');