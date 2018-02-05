<?php
namespace Summoner\ORM;

use \PDO;
use \Summoner\ORM\Database;

class SummonerBuilder
{
  private $stmt;
  private $params = [];

  /*public const JOIN_LEFT = 0;
  public const JOIN_RIGHT = 1;
  public const JOIN_INNER = 2;
  public const JOIN_OUTER = 3;*/

  public function manualRequest($req, $params = [])
  {
    $this->stmt = $req;
    if ( !empty($params) )
      $this->params = $params;

    return $this->process();
  }

  public function select($fields = [])
  {
    $f = [];
    if ( empty($fields) )
      $f[] = "*";
    if ( !is_array($fields) )
      $fields = [$fields];

    foreach ($fields as $k => $v)
    {
      if ( is_string($k) ) $f[] = "{$v} {$k}";
      else $f[] = $v;
    }

    $this->stmt = "SELECT ". implode(", ", $f);
    return $this;
  }
  // Insert
  public function insert($table, $fields = [])
  {
    $this->stmt = "INSERT INTO {$table}";
    if ( !empty($fields) )
      $this->stmt .= " (". (is_array($fields) ? implode(", ", $fields) : $fields) .")";
    return $this;
  }

  public function values($values = [])
  {
    $this->stmt .= " VALUES (". (is_array($values) ? implode(", ", $values) : $values) .")";
    return $this;
  }
  
  public function update($table, $fields)
  {
    $this->stmt = "UPDATE {$table} SET ". (is_array($values) ? implode(", ", $values) : $values);
    return $this;
  }
  
  public function delete($table)
  {
    $this->stmt = "DELETE FROM {$table}";
    return $this;
  }
  
  public function from($table)
  {
    $this->stmt .= " FROM {$table}";
    return $this;
  }
  
  public function where($where = null)
  {
    $this->stmt .= " WHERE {$where}";
    return $this;
  }
  
  public function join($table, $type = "LEFT")
  {
    $this->stmt .= " {$type} JOIN {$table}";
    return $this;
  }

  public function on($v)
  {
    $this->stmt .= " ON {$v}";
    return $this;
  }
  
  public function and($v = null)
  {
    $this->stmt .= " AND {$v}";
    return $this;
  }
 
  public function or($v = null)
  {
    $this->stmt .= " OR {$v}";
    return $this;
  }

  public function orderBy($v)
  {
    $this->stmt .= " ORDER BY {$v}";
    return $this;
  }

  public function groupBy($v)
  {
    $this->stmt .= " GROUP BY {$v}";
    return $this;
  }

  public function between($min, $max)
  {
    $this->stmt .= " BETWEEN {$min} AND {$max}";
    return $this;
  }

  public function like($v)
  {
    $this->stmt .= " LIKE {$v}";
    return $this;
  }

  public function limit(int $number, int $start = 0)
  {
    $this->stmt .= " LIMIT {$start}, {$number}";
    return $this;
  }

  public function in(...$in)
  {
    $this->stmt .= " IN (". implode(", ", $in) .")";
    return $this;
  }

  public function having($v)
  {
    $this->stmt .= " HAVING {$v}";
    return $this;
  }

  public function addParam($key = null, $value)
  {
    if ( $key === null )
      $this->params[] = $value;
    else
      $this->params[$key] = $value;
    return $this;
  }
  
  public function process($type, $params = [])
  {
    if ( !is_array($params) )
      $params = [$params];
    if ( !empty($params) )
      $this->params = array_merge($this->params, $params);
    // dump($this->stmts, $this->params);

    $db = Database::_getInstance()->prepare($this->stmt);
    $res = $db->execute($this->params);
    if ( !$type ) return $res;
    return ($type == "ONE") ? $db->fetch(PDO::FETCH_OBJ) : $db->fetchAll(PDO::FETCH_OBJ);
  }

  public function debug()
  {
    dump($this->stmt, $this->params);
  }

  /*private function typeJoin($type)
  {
    $join = "";
    switch ($type):
      case SummonerBuilder::JOIN_LEFT:
        $join = "LEFT JOIN";
        break;
      case SummonerBuilder::JOIN_RIGHT:
        $join = "RIGHT JOIN";
        break;
      case SummonerBuilder::JOIN_INNER:
        $join = "INNER JOIN";
        break;
      case SummonerBuilder::JOIN_OUTER:
        $join = "OUTER JOIN";
        break;
      default:
        $join = "LEFT JOIN";
        break;
    endswitch;

    return $join;
  }*/
}