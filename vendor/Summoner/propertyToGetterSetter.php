<?php
function propertyToGetterSetter($name)
{
  $name = ucfirst($name);
  $tmp = explode('_', $name);
  $name = implode('', array_map(function($item) { return ucfirst($item); }, $tmp));

  return $name;
}

function getterSetterToProperty($name)
{
  $splited = preg_split("/(?=[A-Z]{1})/", $name);
  array_shift($splited);
  $splited = array_map(function($item) { return strtolower($item); }, $splited);
  return implode('_', $splited);
}