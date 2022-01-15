<?php

use sinri\ark\core\ArkHelper;
use sinri\ark\core\ArkLogger;

require_once __DIR__ . '/../vendor/autoload.php';

$logger = new ArkLogger();

ArkHelper::registerErrorHandlerForLogging($logger, E_WARNING | E_ERROR, true);

$f = @fopen("/tmp/no/such/file", "r");
if ($f) {
    fclose($f);
}
exit(0);


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
// (new DeathMaker())->makeDeath();