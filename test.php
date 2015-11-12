<?php
/**
 *
 */
error_reporting(E_ALL);
require_once("vendor/autoload.php");

$inverter = new \Inverter\HosolaInverter("mattie");
$inverter->fetch();

echo $inverter->getJSON() . PHP_EOL;

$inverter->toPVOutput();
$inverter->toMySQL();