<?php
namespace Inverter;

class HosolaInverter extends Inverter
  {
  /** @var  int */
  public $timestamp;
  /** @var  string */
  public $datetime;
  /** @var  string */
  public $inverter_id;
  /** @var  float */
  public $temperature;
  /** @var  float */
  public $vpv;
  /** @var  float */
  public $ipv;
  /** @var  float */
  public $iac;
  /** @var  float */
  public $vac;
  /** @var  float */
  public $fac;
  /** @var  float */
  public $pac;
  /** @var  float */
  public $e_today;
  /** @var  float */
  public $e_total;
  /** @var  float */
  public $total_hours;

  public function __construct($name)
    {
    parent::__construct($name);

    $this->ip = $this->settings["hosola-inverter"]["ip"];
    $this->port = $this->settings["hosola-inverter"]["port"];
    $this->protocol = $this->settings["hosola-inverter"]["protocol"];
    $this->serial = $this->settings["hosola-inverter"]["serial"];
    //todo verify above info
    }


  public function fetch()
    {
    $error_code = null;
    $error_string = null;
    $this->socket = @stream_socket_client($this->protocol."://" . $this->ip . ":" . $this->port, $error_code, $error_string, 3);

    if ($this->socket === false)
      {
      throw new \Exception("Unable to create socket");
      }
    else
      {
      $databuffer = '';
      // (binary) read data buffer (expected 99 bytes), do not use fgets()
      $databuffer = @fread($this->socket, 128);
      if ($databuffer !== false)
        {
        // get bytes received length
        $bytesreceived = strlen($databuffer);
        // if enough data is returned
        if ($bytesreceived > 90)
          {
          // We have the correct data, now put this in the correct fields
          $this->parseData($databuffer);
          }
        else
          {
          fclose($this->socket);
          throw new \Exception("Incorrect data length, expected 99 bytes but received " . $bytesreceived);
          }
        }
      else
        {
        fclose($this->socket);
        }
      }
    }

  protected function parseData($databuffer)
    {
    $this->timestamp = time(); // todo can we get this from inverter?
    $this->datetime = date('Y-m-d H:i:s', $this->timestamp);

    $this->setTemperature($this->getShort($databuffer, 31, 10));
    $this->setVpv($this->getShort($databuffer, 33, 10, 3));
    $this->setIpv($this->getShort($databuffer, 39, 10, 3));
    $this->setIac($this->getShort($databuffer, 45, 10, 3));
    $this->setVac($this->getShort($databuffer, 51, 10, 3));
    $this->setFac($this->getShort($databuffer, 57, 100, 3, 4));
    $this->setPac($this->getShort($databuffer, 59, 1, 3, 4));
    $this->setEToday($this->getShort($databuffer, 69, 100));
    $this->setETotal($this->getLong($databuffer, 71, 1));
    $this->setTotalHours($this->getLong($databuffer, 75, 1));
    }

  /**
   * Get the Hosola specific info and send it to PVout
   */
  public function toPVOutput()
    {
    $data = [];
    $data["d"] = date("yyyymmdd", $this->getTimestamp());
    $data["t"] = date("hh:mm", $this->getTimestamp());
    $data["v1"] = $this->getEToday();
    $data["v2"] = $this->getPac();
    $data["v5"] = $this->getTemperature();
    $data["v6"] = $this->getVpv();

    var_dump($data);

//    PVOutHelper::sendToPVOut($this->settings, $data);
    }


  public function toMySQL()
    {
    $pdo = new \PDO("mysql:" . $this->settings["mysql"]["host"] . ";dbname=" . $this->settings["mysql"]["database"], $this->settings["mysql"]["user"], $this->settings["mysql"]["password"]);

    $sql = "INSERT INTO " . $this->settings["mysql"]["table"] . "
    (timestamp, etoday)
    VALUES(
    :timestamp,
    :etoday
    )";

    $sth = $pdo->prepare($sql);
    $sth->bindValue(":timestamp", $this->getTimestamp());
    $sth->bindValue(":etodaty", $this->getEToday());

    $sth->execute();
    }

  /**
   * @return string Returns all object vars as a JSON encoded string
   */
  public function getJSON()
    {
    return json_encode(get_object_vars($this));
    }

  /**
   * @return int
   */
  public function getTimestamp()
    {
    return $this->timestamp;
    }

  /**
   * @param int $timestamp
   */
  public function setTimestamp($timestamp)
    {
    $this->timestamp = $timestamp;
    }

  /**
   * @return string
   */
  public function getDatetime()
    {
    return $this->datetime;
    }

  /**
   * @param string $datetime
   */
  public function setDatetime($datetime)
    {
    $this->datetime = $datetime;
    }

  /**
   * @return string
   */
  public function getInverterId()
    {
    return $this->inverter_id;
    }

  /**
   * @param string $inverter_id
   */
  public function setInverterId($inverter_id)
    {
    $this->inverter_id = $inverter_id;
    }

  /**
   * @return float
   */
  public function getTemperature()
    {
    return $this->temperature;
    }

  /**
   * @param float $temperature
   */
  public function setTemperature($temperature)
    {
    $this->temperature = $temperature;
    }

  /**
   * @return float
   */
  public function getVpv()
    {
    return $this->vpv;
    }

  /**
   * @param float $vpv
   */
  public function setVpv($vpv)
    {
    $this->vpv = $vpv;
    }

  /**
   * @return float
   */
  public function getIpv()
    {
    return $this->ipv;
    }

  /**
   * @param float $ipv
   */
  public function setIpv($ipv)
    {
    $this->ipv = $ipv;
    }

  /**
   * @return float
   */
  public function getIac()
    {
    return $this->iac;
    }

  /**
   * @param float $iac
   */
  public function setIac($iac)
    {
    $this->iac = $iac;
    }

  /**
   * @return float
   */
  public function getFac()
    {
    return $this->fac;
    }

  /**
   * @param float $fac
   */
  public function setFac($fac)
    {
    $this->fac = $fac;
    }

  /**
   * @return float
   */
  public function getPac()
    {
    return $this->pac;
    }

  /**
   * @param float $pac
   */
  public function setPac($pac)
    {
    $this->pac = $pac;
    }

  /**
   * @return float
   */
  public function getEToday()
    {
    return $this->e_today;
    }

  /**
   * @param float $e_today
   */
  public function setEToday($e_today)
    {
    $this->e_today = $e_today;
    }

  /**
   * @return float
   */
  public function getETotal()
    {
    return $this->e_total;
    }

  /**
   * @param float $e_total
   */
  public function setETotal($e_total)
    {
    $this->e_total = $e_total;
    }

  /**
   * @return float
   */
  public function getTotalHours()
    {
    return $this->total_hours;
    }

  /**
   * @param float $total_hours
   */
  public function setTotalHours($total_hours)
    {
    $this->total_hours = $total_hours;
    }

  /**
   * @return float
   */
  public function getVac()
    {
    return $this->vac;
    }

  /**
   * @param float $vac
   */
  public function setVac($vac)
    {
    $this->vac = $vac;
    }


  }
