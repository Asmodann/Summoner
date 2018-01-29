<?php
namespace Summoner\Framework\Path;


class Route
{
  private $path;
  private $callable;
  private $matches = [];
  private $params = [];
  private $name = "";
  
  public function __construct(string $path, $callable)
  {
    $this->path = trim($path, "/");
    $this->callable = $callable;
  }

  public function getPath()
  {
    return $this->path;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setName(string $name)
  {
    $this->name = $name;
  }

  public function with($var, $regex)
  {
    $this->params[$var] = str_replace("(", "(?:", $regex);
    return $this;
  }

  public function match($url)
  {
    $url = trim($url, "/");
    $path = preg_replace_callback("/:([\w]+)/", [$this, "paramMatch"], $this->path);
    $regex = "/^".str_replace("/", "\\/", $path)."$/i";

    if (!preg_match($regex, $url, $matches))
      return false;

    array_shift($matches);
    $this->matches = $matches;
    return true;
  }

  public function paramMatch($match)
  {
    if (isset($this->params[$match[1]]))
      return "(".$this->params[$match[1]].")";

    return "([^/]+)";
  }

  public function call()
  {
    if (!is_string($this->callable))
      return call_user_func_array($this->callable, $this->matches);

    $tmp = explode("#", $this->callable);
    $controller = "Summoner\\Controller\\".array_shift($tmp);
    $action = !empty($tmp) ? array_shift($tmp) : "index";

    if (!class_exists($controller) || !method_exists($controller, $action))
      return false;

    return call_user_func_array([new $controller, $action], $this->matches);
  }
}