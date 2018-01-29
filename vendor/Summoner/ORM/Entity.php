<?php
namespace Summoner\ORM;

abstract class Entity
{
  protected $table_name;
  protected $fields_type;

  public function __construct()
  {
    $this->table_name = (new \ReflectionClass($this))->getShortName();
  }

  public function getTableName()
  {
    return getterSetterToProperty($this->table_name);
  }

  public function getClassName()
  {
    return $this->table_name;
  }
}