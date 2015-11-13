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
    $http_request = new \HttpRequest($settings["pvout"]["url"], \HttpRequest::METH_POST);
    $headers["X-Pvoutput-Apikey"] = $settings["pvout"]["api_key"];
    $headers["X-Pvoutput-SystemId"] = $settings["pvout"]["system_id"];
    $http_request->setHeaders($headers);
    $http_request->addPostFields($data);
    try
      {
      $output = $http_request->send()->getBody();
      $logger->addInfo("Data send to pvoutput!", ["output" => $output]);
      }
    catch (\HttpException $e)
      {
      $logger->addWarning("Unable to connect to pvoutput", ["error" => $e->getMessage()]);
      }
    }
  }