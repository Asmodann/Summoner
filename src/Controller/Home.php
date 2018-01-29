<?php
namespace Summoner\Controller;

use \Summoner\Framework\Controller;

class Home extends Controller
{
  public function index()
  {
    return $this->render(__FUNCTION__);
  }
}