<?php
namespace Summoner\Framework\Path;

use \Summoner\Framework\Controller;

class Router
{
  private $url;
  private $routes = [];
  private $routes_error = [];
  private static $self;


  public function __construct(string $url)
  {
    if ( static::$self === null )
      static::$self = $this;

    static::$self->url = $url;
  }

  public static function _getSelf()
  {
    return self::$self;
  }

  public function get($path, $callable)
  {
    return static::$self->add($path, $callable, "GET");
  }

  public function post($path, $callable)
  {
    return static::$self->add($path, $callable, "POST");
  }

  public function setRoutesError($name, $type)
  {
    // $GLOBALS["ENVIRONMENT_DEV"];
    $name = str_replace(".html", "", $name);
    $this->routes_error[$type] = "{$name}.html";
    return $this;
  }

  public function getRoutesError($type)
  {
    if ( !array_key_exists($type, $this->routes_error))
      return false;

    return $this->routes_error[$type];
  }

  private function add($path, $callable, $type)
  {
    $route = new Route($path, $callable);
    static::$self->routes[$type][] = $route;
    return $route;
  }

  public function byName($name)
  {
    foreach (static::$self->routes["GET"] as $route)
    {
      if ( $route->getName() == $name ) return $route;
    }
    return false;
  }

  public function run()
  {
    $method = $_SERVER["REQUEST_METHOD"];
    if (!isset(static::$self->routes[$method]))
      return false;

    foreach (static::$self->routes[$method] as $route)
    {
      if ($route->match(static::$self->url))
        return $route->call();
    }
    if ( array_key_exists("notfound", $this->routes_error) )
      return (new Controller())->pageError($this->routes_error["notfound"], "notfound");

    return false;
  }
}