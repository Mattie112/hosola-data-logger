<?php

require_once "vendor/autoload.php";

$inv = new \Inverter\HosolaInverter(new \Monolog\Logger("log"));

$var = $inv->calculateIDString(611234567);

echo "ID String:".PHP_EOL;
var_dump($var);
echo "Base64 encoded ID string".PHP_EOL;
var_dump(base64_encode($inv->calculateIDString(613758394)));

echo "Int values per byte:".PHP_EOL;
for($i = 0; $i < strlen($var); $i++)
{
    echo ord($var[$i])." ";
}
echo PHP_EOL;

echo "Hex values per byte:".PHP_EOL;
for($i = 0; $i < strlen($var); $i++)
{
    echo dechex(ord($var[$i]))." ";
}
echo PHP_EOL;
