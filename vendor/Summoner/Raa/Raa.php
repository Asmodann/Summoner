<?php
namespace Summoner\Raa;

class Raa
{
  protected $key = "";
  protected $data = [];
  protected $data_header = [];

  public function __construct($key = null)
  {
    if ( $key == null )
      $this->key = $GLOBALS["API_KEY"];
    else
      $this->key = $key;
  }

  public function setKey($value)
  {
    $this->key = $key;
    return $this;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function addData($key, $value)
  {
    $this->data[$key] = $value;
  }

  public function addDataHeader($key, $value)
  {
    $this->data_header[$key] = $value;
  }

  public function removeData($key)
  {
    if ( array_key_exists($key, $this->data) )
      unset($this->data[$key]);

    return false;
  }

  public function removeDataHeader($key)
  {
    if ( array_key_exists($key, $this->data_header) )
      unset($this->data_header[$key]);

    return false;
  }

  final public function processRequest(string $url, $type = "GET")
  {
    $curl = curl_init();
    dump(http_build_query($this->data));
    if ( $type == "POST" )
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->data));
    else if ( !empty($this->data) )
      $url = $url."?".http_build_query($this->data);
    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => $type,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_HTTPHEADER => $this->arrayToHeader($this->data_header),
    ]);
    $response = curl_exec($curl);
    $results = json_decode($response, true);
    return $results;
  }

  final public function processRequestUrl(string $url)
  {
    $data = array_merge($this->data, $this->data_header);
    $url = $url."%s".http_build_query($data);
    if ( !preg_match("/\?(.*)$/", $url) )
      $url = str_replace("%s", "?", $url);
    else
      $url = str_replace("%s", "&", $url);
    $response = file_get_contents($url);
    $results = json_decode($response, true);
    return $results;
  }

  final protected function arrayToHeader($array)
  {
    $header = [];
    foreach ($array as $key => $value)
      $header[] = "$key: $value";

    return $header;
  }
}