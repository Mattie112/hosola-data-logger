<?php
namespace Inverter;

use Monolog\Logger;

abstract class Inverter
  {
  public $ip;
  public $port;
  public $protocol = "tcp";
  public $serial;

  /** @var  Logger */
  protected $logger;

  protected $socket;
  protected $settings;

  /**
   * Inverter constructor.
   * @param Logger $logger
   */
  public function __construct(Logger $logger)
    {
    $this->logger = $logger;
    $this->settings = parse_ini_file(__DIR__ . "/../config.ini", true);
    }

  abstract function fetch();

  protected abstract function parseData($databuffer);

  /**
   * https://github.com/micromys/Omnik
   *
   * @param $databuffer
   * @param int $start
   * @param int $divider
   * @return float
   */
  protected function getLong($databuffer, $start = 71, $divider = 10)        // get Long
    {
    $t = floatval($this->str2dec(substr($databuffer, $start, 4)));        // convert 4 bytes to decimal
    return $t / $divider;                  // return value/divder
    }

  /**
   * https://github.com/micromys/Omnik
   *
   * @param $databuffer
   * @param int $start
   * @param int $divider
   * @param int $iterate
   * @param int $offset
   * @return float|int|void
   */
  protected function getShort($databuffer, $start = 59, $divider = 10, $iterate = 0, $offset = 2)      // return (optionally repeating) values
    {
    if ($iterate == 0)                          // 0 = no repeat, return one value
      {
      $t = floatval($this->str2dec(substr($databuffer, $start, 2)));        // convert to decimal 2 bytes
      return ($t == 65535) ? 0 : $t / $divider;          // if 0xFFFF return 0 else value/divder
      }
    else
      {
      $iterate = min($iterate, 3);                    // max iterations = 3
      for ($i = 1; $i <= $iterate; $i++)
        {
        $t = floatval($this->str2dec(substr($databuffer, $start + $offset * ($i - 1), 2)));  // convert two bytes from databuffer to decimal
        return ($t == 65535) ? 0 : $t / $divider;        // if 0xFFFF return 0 else value/divder
        }
      }
    return false;
    }

  /**
   * https://github.com/micromys/Omnik
   *
   * @param $string
   * @return int
   */
  function str2dec($string)              // convert string to decimal	i.e. string = 0x'0101' (=chr(1).chr(1)) => dec = 257
    {
    $str = strrev($string);              // reverse string 0x'121314'=> 0x'141312'
    $dec = 0;                  // init
    for ($i = 0; $i < strlen($string); $i++)        // foreach byte calculate decimal value multiplied by power of 256^$i
      {
      $dec += ord(substr($str, $i, 1)) * pow(256, $i);    // take a byte, get ascii value, muliply by 256^(0,1,...n where n=length-1) and add to $dec
      }
    return $dec;                // return decimal
    }

  /**
   * @return mixed
   */
  public function getIp()
    {
    return $this->ip;
    }

  /**
   * @param mixed $ip
   */
  public function setIp($ip)
    {
    $this->ip = $ip;
    }

  /**
   * @return mixed
   */
  public function getPort()
    {
    return $this->port;
    }

  /**
   * @param mixed $port
   */
  public function setPort($port)
    {
    $this->port = $port;
    }

  /**
   * @return string
   */
  public function getProtocol()
    {
    return $this->protocol;
    }

  /**
   * @param string $protocol
   */
  public function setProtocol($protocol)
    {
    $this->protocol = $protocol;
    }

  /**
   * @return mixed
   */
  public function getSerial()
    {
    return $this->serial;
    }

  /**
   * @param mixed $serial
   */
  public function setSerial($serial)
    {
    $this->serial = $serial;
    }
  }