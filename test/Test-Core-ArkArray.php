<?php

use sinri\ark\core\ArkArray;

require_once __DIR__ . '/../vendor/autoload.php';

$array = ['a' => ['a1', 'a2'], 'b' => ['b1' => ['b11']], 'c' => 0, 'd' => null, 'e' => false, 'f' => 'miao'];

$a = new ArkArray($array);

//unset($array);

foreach ($a as $key => $value) {
    echo $key . ' : ' . json_encode($value) . PHP_EOL;
}

$x = $a->read(['a', 1]);
echo $x . PHP_EOL;
$x = $a->read(['a', 2], 3);
echo $x . PHP_EOL;

$x = $a->validateKeychain(['a', 1]);
echo json_encode($x) . PHP_EOL;
$x = $a->validateKeychain(['a', 2]);
echo json_encode($x) . PHP_EOL;

$a->write(['a', 2], 4);

$a->changeAllKeysToUpperCase();

var_dump($a);

var_dump($array);


echo json_encode($a->getRawArray() === $array) . PHP_EOL;

echo '-----' . PHP_EOL;

$y = (new ArkArray());
echo $y->push('x1')->push('x2')->push('x3')->pop() . PHP_EOL;
var_dump($y->getRawArray());