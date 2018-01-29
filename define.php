<?php
define("BASE_URI", substr(__DIR__,strlen($_SERVER["DOCUMENT_ROOT"])));
define("ROOT", str_replace("index.php", "", $_SERVER["SCRIPT_FILENAME"]));
define("WEBROOT", str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]));

return [
  "BASE_URI" => BASE_URI,
  "ROOT" => ROOT,
  "WEBROOT" => WEBROOT,
];