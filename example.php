<?php
/**
 * Example script that outputs the data to the console
 */
error_reporting(E_ALL);
require_once("vendor/autoload.php");

$settings = parse_ini_file("config.ini", true);

$logger = new \Monolog\Logger("log");
$logger->pushHandler(new \Monolog\Handler\StreamHandler("php://stdout", $settings["logging"]["cli_log_level"]));

if($settings["logging"]["file_log_enabled"])
  {
  $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings["logging"]["file_log"], $settings["logging"]["file_log_level"]));
  }

$logger->addInfo("Starting script to fetch live data from Hosola Inverter");

$inverter = new \Inverter\HosolaInverter($logger);
$inverter->fetch();

echo PHP_EOL . "Data received from interter:" . PHP_EOL;
echo $inverter->getJSON() . PHP_EOL;