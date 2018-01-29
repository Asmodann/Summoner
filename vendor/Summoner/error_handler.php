<?php
set_error_handler("customError");

use \Summoner\Framework\Controller;
use \Summoner\Framework\Path\Router;

function customError($errno, $errstr, $errfile, $errline, $errcxt)
{
  $file_500 = Router::_getSelf()->getRoutesError("internal");
  if ( !$GLOBALS["ENVIRONMENT_DEV"] )
  {
    die( (new Controller())->pageError($file_500, "internal") );
  }

  echo "<div style=\"background-color: #F66; padding: 10px;\">";
  echo "<b>Error:</b> [{$errno}] {$errstr}<br />";
  echo "<b>File:</b> {$errfile} <b>on line</b> {$errline}<br />";
  echo "Ending script...</div>";
  die();
}