<?php
namespace Summoner\Framework;

use \Summoner\Syr\View;
use \Summoner\ORM\SummonerMapping;
use \Summoner\Framework\Path\Router;
use \Summoner\Raa\Raa;

class Controller
{
  protected $summoner;
  protected $raa;
  protected $variables = [];

  final protected function getSummoner()
  {
    $this->summoner = new SummonerMapping();
    return $this->summoner;
  }

  final protected function getRaa()
  {
    $this->raa = new Raa();
    return $this->raa;
  }

  final protected function assign($key, $value)
  {
    $this->variables[$key] = $value;
  }

  final protected function renderJSON($status = 200, $data = [])
  {
    if ( !empty($data) )
      $data = array_merge($data, $this->variables);
    else
      $data = $this->variables;

    header("Content-type: Application/JSON");
    return json_encode(["data" => $data, "status" => $status]);
  }

  final protected function render($view, $params = [], $is_file = false)
  {
    if ( !empty($params) )
      $this->variables = array_merge($this->variables, $params);

    $reflection = new \ReflectionClass(get_class($this));
    $View = new View($this->variables);
    return $View->render($reflection->getShortName(), $view, $is_file);
  }

  final protected function partialRender($view, $params = [], $is_file = false)
  {
    if ( !empty($params) )
      $this->variables = array_merge($this->variables, $params);

    $reflection = new \ReflectionClass(get_class($this));
    $View = new View($this->variables);
    return $View->partialRender($reflection->getShortName(), $view, $is_file);
  }

  final protected function redirect($path = null)
  {
    header("Location: ".WEBROOT.$path);
    return;
  }

  final protected function redirectToRoute($route_name, $params = [])
  {
    $Router = Router::_getSelf();
    $route = $Router->byName($route_name);
    if ( !$route )
      trigger_error("Error Processing Request: No route name \"".$route_name."\" founded");
    
    $this->redirect($route->getPath());
  }

  final public function pageError($name, $type = "notfound")
  {
    $path = __DIR__."/../../../templates/error/{$name}";
    if ( file_exists($path) )
    {
      $func = "{$type}_error";
      return $this->$func($path);
    }
    return false;
  }

  private function notfound_error($path)
  {
    // if ( $GLOBALS["PAGE_NOT_FOUND_LAYOUT"] )
    return $this->render($path, [], true);
  }

  private function internal_error($path)
  {
    // if ( $GLOBALS["PAGE_INTERNAL_ERROR_LAYOUT"] )
    return $this->render($path, [], true);
  }
}