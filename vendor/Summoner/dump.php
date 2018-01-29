<?php
function dump(...$vars)
{
  echo "<pre>";
  foreach ($vars as $var)
    var_dump($var);
  echo "</pre>";
}

function dd(...$vars)
{
  dump($vars);
  die();
}