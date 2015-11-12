<?php
/**
 * Created by PhpStorm.
 * User: Matthijs
 * Date: 12-11-2015
 * Time: 19:39
 */

namespace Inverter;


class PVOutHelper
  {

  /**
   * Settings with [pvout][apikey] and [pvout][system_id]
   * data formatted how pvoout it wants http://pvoutput.org/help.html#api-spec
   *
   * @param $settings
   * @param $data
   * @throws \Exception
   */
  public static function sendToPVOut($settings, $data)
    {

    $http_request = new \HttpRequest($settings["pvout"]["url"], \HttpRequest::METH_POST);
    $headers["X-Pvoutput-Apikey"] = $settings["pvout"]["api_key"];
    $headers["X-Pvoutput-SystemId"] = $settings["pvout"]["system_id"];
    $http_request->setHeaders($headers);
    $http_request->addPostFields($data);
    try
      {
      $http_request->send()->getBody();
      }
    catch (\HttpException $e)
      {
      throw new \Exception("Unable to connect to pvoutput: " . $e->getMessage());
      }
    }
  }