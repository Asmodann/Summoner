<?php
namespace Summoner\Console;

class Shell
{
  protected $command;
  protected $value;
  protected $options;
  
  public function __construct($args)
  {
    array_shift($args);
    $this->command = array_shift($args);
    $this->value = array_shift($args);
    $this->options = array_shift($args);
  }

  public function dispatch()
  {
    $commands = require(__DIR__."/commands.php");
    $list = explode(':', $this->command);
    $cmd = array_shift($list);
    $subcmd = array_shift($list);
    if ( !array_key_exists($cmd, $commands) )
      die('Not in array');

    $cmd = "\Summoner\Console\Generator\\".ucfirst($cmd);
    $subcmd = "do".ucfirst($subcmd);
    (new $cmd($this->value, $this->options))->$subcmd();
  }

}