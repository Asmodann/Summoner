<?php
namespace Summoner\ORM;

use \PDO;
use \Summoner\ORM\Database;

class SummonerMapping
{
  static $instance_ = null;
  protected $stmt = false;
  protected $entity;

  public function __construct()
  {
    if (self::$instance_ !== null)
      return $this;

    self::$instance_ = Database::_getInstance();

    return $this;
  }

  public function loadEntity($entity)
  {
    $this->entity = $entity;
    return $this;
  }

  public function takeEntity()
  {
    return new $this->entity();
  }

  public function newBuilder()
  {
    return new SummonerBuilder();
  }

  protected function dataToObject($data, $entity = false, $relation)
  {
    if (!$entity)
      $entity = $this->entity;
    foreach ($data as $key => $value) {
      if ( !property_exists($entity, $key) )
        continue;
      $key = "set".propertyToGetterSetter($key);
      $entity->$key($value);
    }
    if ( file_exists(__DIR__."/../../../src/Entity/relations/{$entity->getClassName()}.relation.php") && $relation == true )
    {
      $relations = require(__DIR__."/../../../src/Entity/relations/{$entity->getClassName()}.relation.php");
      $this->mappingObject($relations, $entity);
    }
    return $entity;
  }

  protected function execute($value, $entity, $relation)
  {
    $this->stmt->setFetchMode(PDO::FETCH_OBJ);
    $this->stmt->execute($value);
    $data = $this->stmt->fetch();
    if ( !$data ) return false;
    return $this->dataToObject($data, $entity, $relation);
  }

  protected function executeAll($value, $entity, $relation)
  {
    
  }

  public function find($value, $entity = false, $relation = true)
  {
    if ( !$entity )
      $entity = new $this->entity();
    $this->stmt = self::$instance_->prepare("SELECT * FROM {$entity->getTableName()} WHERE id = ?");
    return $this->execute([$value], $entity, $relation);
  }

  public function findBy($field, $value, $entity = false, $relation = true)
  {
    if ( !$entity )
      $entity = new $this->entity();
    $this->stmt = self::$instance_->prepare("SELECT * FROM {$entity->getTableName()} WHERE {$field} = ?");
    return $this->execute([$value], $entity, $relation);
  }

  public function findAll($value = 0, $field = null, $entity = null, $relation = true)
  {
    $str = " WHERE {$field} = ?";
    if ($value == 0 && $field == null)
      $str = "";
    if ($entity == null)
      $entity = new $this->entity();

    $this->stmt = self::$instance_->prepare("SELECT * FROM {$entity->getTableName()}{$str} ORDER BY {$field} DESC");
    $this->stmt->setFetchMode(PDO::FETCH_OBJ);

    if ( empty($str) )
      $this->stmt->execute();
    else
      $this->stmt->execute([$value]);

    $data = $this->stmt->fetchAll();
    if ( !$data ) return false;
    $entities = [];
    foreach ($data as $value)
      $entities[] = $this->dataToObject($value, new $entity, $relation);
    return $entities;
  }

  public function save(&$entity)
  {
    $schema = require(__DIR__."/../../../src/Entity/schemas/{$entity->getClassName()}.schema.php");
    foreach ($schema['_primary'] as $value)
    {
      $get = "get".propertyToGetterSetter($value);
      if ( $entity->$get() === null )
        return $this->insert($entity, $schema);
    }
    return $this->update($entity, $schema);
  }

  public function delete(&$entity, $field = "id", $recursive = false)
  {
    // Delete entity via $field
    // if $recursive => delete all relations
  }

  protected function insert(&$entity, $schema)
  {
    $fields = [];
    $values = [];
    $what = [];
    foreach ($schema as $key => $value)
    {
      if ( $key === '_primary' )
        continue;
      $get = "get".propertyToGetterSetter($value);
      $values[] = $entity->$get();
      $fields[] = $value;
      $what[] = '?';
    }
    $what = implode(', ', $what);
    $fields = implode(', ', $fields);
    $this->stmt = self::$instance_->prepare("INSERT INTO {$entity->getTableName()} ({$fields}) VALUES ({$what})");
    $this->stmt->execute($values);
    return $this->find(self::$instance_->lastInsertId(), $entity);
  }

  protected function update(&$entity, $schema)
  {
    $fields = [];
    $values = [];
    $pkeys = [];
    $pvalues = [];
    foreach ($schema as $key => $value)
    {
      if ( $key === '_primary' )
      {
        foreach ($value as $f)
        {
          $get = "get".propertyToGetterSetter($f);
          $pkeys[] = $f.' = ?';
          $pvalues[] = $entity->$get();
        }
        continue;
      }
      $get = "get".propertyToGetterSetter($value);
      $values[] = $entity->$get();
      $fields[] = $value.' = ?';
    }
    $fields = implode(', ', $fields);
    $pkeys = implode(' AND ', $pkeys);
    $values = array_merge($values, $pvalues);
    $this->stmt = self::$instance_->prepare("UPDATE {$entity->getTableName()} SET {$fields} WHERE {$pkeys}");
    $this->stmt->execute($values);
  }

  protected function mappingObject($relations, &$entity)
  {
    foreach ($relations as $attr => $options)
    {
      $obj = new $options['class']();
      $func = $options['type'];
      $set = "set".propertyToGetterSetter($attr);
      $entity->$set($this->$func($obj, $options, $entity));
    }
  }

  protected function OneToMany($class, $relation, $entity)
  {
    $out = $relation['field_out'];
    $in = 'id';
    if ( isset($relation['field_in']) )
      $in = $relation['field_in'];
    $getSearch = "get".propertyToGetterSetter($in);
    return $this->findAll($entity->$getSearch(), $out, $class, false);
  }

  protected function OneToOne($class, $relation, $entity)
  {
    $out = $relation['field_out'];
    $in = 'id';
    if ( isset($relation['field_in']) )
      $in = $relation['field_in'];
    $getSearch = "get".propertyToGetterSetter($in);
    return $this->findBy($out, $entity->$getSearch(), $class, false);
  }
}