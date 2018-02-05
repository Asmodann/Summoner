<?php
namespace Summoner\Syr;


class View
{
  private $vars = [];
  private $content;
  private $layout = "default";


  public function __construct($vars)
  {
    $this->vars = $vars;
  }

  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  private function hasView($class, $view)
  {
    $view = "$view.syr.html";
    $path = ROOT.implode(DIRECTORY_SEPARATOR, ["templates", "views", $class, $view]);
    return (file_exists($path) ? $path : false);
  }

  private function hasLayout($layout)
  {
    $layout = "$layout.syr.html";
    $path = ROOT.implode(DIRECTORY_SEPARATOR, ["templates", "layouts", $layout]);
    return (file_exists($path) ? $path : false);
  }

  public function render($class, $view, $is_file)
  {
    $tview = $view;
    if ( !$is_file )
    {
      $view = $this->hasView($class, $view);
    }
    if (!$view)
      trigger_error("Cannot load view: {$tview}.syr.html on class {$class}");
      $this->content = file_get_contents($view);
      
    $this->replaceSyr();
    $this->replaceContent();
    $this->replaceConditions();
    $this->replaceLoop();

    foreach ($this->vars as $key => $value) {
       $$key = $value;
    }
    ob_start();
      eval( "?>".$this->content."<?php" );
    return ob_get_clean();
  }

  public function partialRender($class, $view, $is_file)
  {

    if ( !$is_file )
    {
      $tmp = explode("#", $view);
      $view = array_shift($tmp);
      $view = $this->hasView($class, $view);
      if (!$view)
        trigger_error("Cannot load view: $view");
    }

    $this->content = file_get_contents($view);

    $this->replaceContent();
    $this->replaceConditions();
    $this->replaceLoop();

    foreach ($this->vars as $key => $value) {
       $$key = $value;
    }
    ob_start();
      eval( "?>".$this->content."<?php" );
    return ob_get_clean();
  }

  private function replaceSyr()
  {
    $this->content = preg_replace_callback("/{% template (.*) }/Us", function($match) {
      array_shift($match);
      $file = array_shift($match);
      ob_start();
        require(__DIR__."/../../../templates/layouts/{$file}.syr.html");
      return ob_get_clean();
    }, $this->content);
    preg_replace_callback("/{% block\[(.*)\] }(.*){% endblock }/Us", function($match) {
      $m = array_shift($match);
      $block = array_shift($match);
      $content = array_shift($match);
      $this->content = preg_replace("/{% block {$block} }/U", $content, $this->content);
      $this->content = preg_replace("/{% block\[(.*)\] }(.*){% endblock }/Us", "", $this->content);
    }, $this->content);
  }

  private function replaceContent()
  {
    $this->content = preg_replace_callback("/{%= ([A-Z]+) }/", function($match) {
      if ( array_key_exists($match[1], $GLOBALS["_CONFIG"]) )
        return $GLOBALS["_CONFIG"][$match[1]];
      return $match[0];
    }, $this->content);
    $this->content = preg_replace('/\<\!\-\- (.*) \-\-\>/Usx', '', $this->content);
    $this->content = preg_replace('/{%= if (.*) then (.*) else (.*) }/U', '<?= ($1 ? $2 : $3); ?>', $this->content);
    $this->content = preg_replace('/{%= (.*) }/Ux', '<?= $1 ?>', $this->content);
  }

  private function replaceConditions()
  {
    $this->content = preg_replace("/{% if\s?(.*) }/", "<?php if ($1): ?>", $this->content);
    $this->content = preg_replace("/{% elif\s?(.*) }/", "<?php elseif ($1): ?>", $this->content);
    $this->content = preg_replace("/{% else }/", "<?php else: ?>", $this->content);
    $this->content = preg_replace("/{% endif }/", "<?php endif; ?>", $this->content);
  }

  private function replaceLoop()
  {
    $this->content = preg_replace("/{% for\s?(.*) }/", "<?php for ($1): ?>", $this->content);
    $this->content = preg_replace("/{% endfor }/", '<?php endfor; ?>', $this->content);
    $this->content = preg_replace("/{% each\s?(.*) in (.*) }/", '<?php foreach ($2 as $1): ?>', $this->content);
    $this->content = preg_replace("/{% endeach }/", '<?php endforeach; ?>', $this->content);
  }

}