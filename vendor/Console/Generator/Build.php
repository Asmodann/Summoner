<?php
namespace Summoner\Console\Generator;

use \PDO;
use \Summoner\ORM\Database;

class Build
{
  private $value;
  private $options;

  public function __construct($value, $options)
  {
    $this->value = $value;
    $this->options = $options;
  }

  private function entityGetSet($var, $get, $set)
  {
    $content = "
  public function $get()
  {
    return \$this->$var;
  }
  public function $set(\$value)
  {
    \$this->$var = \$value;
    return \$this;
  }";
  return $content;
  }

  private function entitySchema($fields)
  {
    $content = "<?php

return [
\"_primary\" => [],
%fields%
];";
  $arr_fields = [];
  foreach ($fields as $value)
    $arr_fields[] = "\"".$value['COLUMN_NAME']."\"";

  return str_replace("%fields%", implode(",\n", $arr_fields), $content);
  }

  public function doEntity()
  {
    echo "Generate: ".$this->value."\n";
    $db = Database::_getInstance();
    $class_name = propertyToGetterSetter($this->value);
    $result = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$this->value}'");
    $fields = $result->fetchAll();
    $attra = [];
    $body = [];
    $content = "<?php
namespace Summoner\Entity;

use \Summoner\ORM\Entity;

class {$class_name} extends Entity
{
%attributs%

%getset%
}";
    foreach ($fields as $value)
    {
      $var = $value['COLUMN_NAME'];
      $get = "get".propertyToGetterSetter($var);
      $set = "set".propertyToGetterSetter($var);
      $attr[] = "
  protected \${$var};";
      $body[] = $this->entityGetSet($var, $get, $set);
    }
    $attr = implode("\n", $attr);
    $body = implode("\n", $body);
    $content = str_replace("%attributs%", $attr, $content);
    $content = str_replace("%getset%", $body, $content);
    file_put_contents(__DIR__."/../../../src/Entity/".$class_name.".php", $content);
    file_put_contents(__DIR__."/../../../src/Entity/schemas/".$class_name.".schema.php", $this->entitySchema($fields));
  }

  public function doController()
  {
    $content = "<?php
namespace Summoner\Controller;

use \Summoner\Framework\Controller;

class {$this->value} extends Controller
{
  public function index()
  {
    return \$this->render(__FUNCTION__);
  }
}";
  $viewc = "<p>
  <b>Controller: </b>$this->value
</p>
<p>Welcome on your first view.</p>
";
  if ( !file_exists(__DIR__."/../../../templates/views/$this->value") && !mkdir(__DIR__."/../../../templates/views/$this->value") )
  {
    die("Permission error to create folder templates/views/$this->value");
  }
  file_put_contents(__DIR__."/../../../src/Controller/".$this->value.".php", $content);
  file_put_contents(__DIR__."/../../../templates/views/".$this->value."/index.syr.html", $viewc);
  echo "Generate controller: ".$this->value."\n";
  echo "Generate view: ".$this->value."/index.syr.html\n";
  }
}