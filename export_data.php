<?php
/**
 * Example script that sends the data to PVOutput
 */
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
$inverter->toPVOutput();
$inverter->toMySQL();