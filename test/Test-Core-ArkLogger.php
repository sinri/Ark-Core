<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/2/13
 * Time: 16:59
 */

use Psr\Log\LogLevel;
use sinri\ark\core\ArkLogger;
use sinri\ark\core\ArkLoggerBuffer;

require_once __DIR__ . '/../vendor/autoload.php';
//require_once __DIR__ . '/../autoload.php';

$logger = new ArkLogger(__DIR__ . '/log', 'core-logger-test');
$logger->debug(LogLevel::DEBUG, [LogLevel::DEBUG]);
$logger = new ArkLogger(__DIR__ . '/log', 'core-logger-test');
$logger->critical(LogLevel::CRITICAL, [LogLevel::CRITICAL]);

// validate prefix
$logger = new ArkLogger(__DIR__ . '/log', 'Aa0/Bb1');
$logger->setIgnoreLevel(LogLevel::ERROR);
$logger->alert("file prefix should be Aa0_Bb1");

// 2.2

$logger = new ArkLogger(__DIR__ . '/log', 'core-logger-test', 'Ymd');
$logger->info("YMD is good");

$logger = new ArkLogger(__DIR__ . '/log', 'core-logger-test', null);
$logger->info("No Rotating is good");

// 2.3

$buffer = new ArkLoggerBuffer(5, function ($items) {
    echo "OUTPUT BUFFER! Current size = " . count($items) . PHP_EOL;
    foreach ($items as $item) {
        echo "> " . json_encode($item) . PHP_EOL;
    }
    // this return is very important, or the buffer would not be cleared
    return true;
});
$logger = new ArkLogger(__DIR__ . '/log', 'buffer-test', 'Ymd', $buffer);
for ($i = 0; $i < 12; $i++) {
    echo "Round $i -> " . microtime(true) . PHP_EOL;
    $logger->info("random: " . uniqid(), [$i]);
}
echo "FIN -> " . microtime(true) . PHP_EOL;

// 2.5

$logger = new ArkLogger(__DIR__ . '/log', '', 'Ymd', null, true);
$logger->info("without prefix");
$logger = new ArkLogger(__DIR__ . '/log', 'x', 'Ymd', null, true);
$logger->info("with prefix");