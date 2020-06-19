<?php

use sinri\ark\core\ArkHelper;
use sinri\ark\core\ArkLogger;

require_once __DIR__ . '/../vendor/autoload.php';

$logger = new ArkLogger();

ArkHelper::registerErrorHandlerForLogging($logger, E_WARNING | E_ERROR, true);

class DeathMaker
{
    function makeDeath()
    {
        $x = 0;
        $y = $x + $z;
        $y *= 3 / 0;

        yy();
    }
}

(new DeathMaker())->makeDeath();