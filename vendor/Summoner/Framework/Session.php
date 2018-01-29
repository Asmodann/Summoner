<?php
namespace Summoner\Framework;

class Session
{
  private $session = [];

  public function __construct()
  {
    if (!isset($_SESSION))
      trigger_error("Session is not initialized!");

    $this->session = &$_SESSION;
  }

  public function get($key = false)
  {
    if ($key)
      return isset($this->session[$key]) ? $this->session[$key] : false;
    
    return $this->session;
  }

  public function set($key, $value)
  {
    $this->session[$key] = $value;
  }

  public function remove($key)
  {
    if (!isset($this->session[$key]))
      return false;

    unset($this->session[$key]);
    return true;
  }

  public function unset()
  {
    session_unset();
  }
  
  public function reset()
  {
    session_destroy();
    session_start();
  }

}