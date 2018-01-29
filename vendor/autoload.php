<?php

spl_autoload_register(function($class) {
  $gclass = $class;
  
  // array_shift($tmp);
  
  if ( autoload_loadVendor($class) )
    return true;
  else if ( autoload_loadSrc($class) )
    return true;
  die("Cannot load class {$gclass}");
});

function autoload_loadVendor($class)
{
  $tmp = explode("\\", $class);
  if ( preg_match("/Console/", $class) )
    array_shift($tmp);
  $class = [ucfirst(array_pop($tmp))];
  $class_name = $class[0];
  $tmp = array_merge($tmp, $class);
  $tmp = implode("/", $tmp);
  // echo __DIR__."/{$tmp}.php<br />";
  if ( !file_exists(__DIR__."/{$tmp}.php") )
    return false;
    
  require(__DIR__."/{$tmp}.php");
  return true;
}

function autoload_loadSrc($class)
{
  $tmp = explode("\\", $class);
  array_shift($tmp);
  $class = [ucfirst(array_pop($tmp))];
  $class_name = $class[0];
  $tmp = array_merge($tmp, $class);
  $tmp = implode("/", $tmp);
  if ( !file_exists(__DIR__."/../src/{$tmp}.php") )
    return false;
    
  require(__DIR__."/../src/{$tmp}.php");
  return true;
}