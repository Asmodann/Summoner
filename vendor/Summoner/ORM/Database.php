<?php
namespace Summoner\ORM;

use \PDO;

class Database
{
  private static $_instance;

  public static function _getInstance()
  {
    if ( self::$_instance == null )
      {
        self::$_instance = new \PDO(
          'mysql:host='.$GLOBALS['DB_HOST'].';dbname='.$GLOBALS['DB_NAME'],
          $GLOBALS['DB_USER'],
          $GLOBALS['DB_PWD'],
          [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
        );
      }
    return self::$_instance;
  }
}