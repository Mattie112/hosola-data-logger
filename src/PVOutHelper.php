<?php
namespace Inverter;

use Monolog\Logger;

/**
 * Class PVOutHelper
 * @package Inverter
 */
class PVOutHelper
  {

  /**
   * Settings with [pvout][apikey] and [pvout][system_id]
   * data formatted how pvoout it wants http://pvoutput.org/help.html#api-spec
   *
   * @param $settings
   * @param $data
   * @param Logger $logger
   * @throws \Exception
   */
  public static function sendToPVOutput($settings, $data, Logger $logger)
    {
    $headers = "Content-type: application/x-www-form-urlencoded\r\n";
    $headers .= "X-Pvoutput-Apikey: ".$settings["pvoutput"]["api_key"]."\r\n";
    $headers .= "X-Pvoutput-SystemId: ".$settings["pvoutput"]["system_id"]."\r\n";

    $options = [
        "http" => [
            "header" => $headers,
            "method" => "POST",
            "content" => http_build_query($data)
        ]
    ];

    try
      {
      $context = stream_context_create($options);
      $output = file_get_contents($settings["pvoutput"]["url"], false, $context);

      $logger->addInfo("Data send to pvoutput!", ["output" => $output]);
      }
    catch(\HttpException $e)
      {
      $logger->addWarning("Unable to connect to pvoutput", ["error" => $e->getMessage()]);
      }
    }
  }