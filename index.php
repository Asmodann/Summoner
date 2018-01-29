<?php
$defines = require("define.php");
require(__DIR__."/vendor/autoload.php");
require(__DIR__."/vendor/Summoner/propertyToGetterSetter.php");
require(__DIR__."/vendor/Summoner/dump.php");
require(__DIR__."/vendor/Summoner/error_handler.php");

$GLOBALS = array_merge($GLOBALS, require("config_env.php"));
$GLOBALS["_CONFIG"] = require("config_global.php");
$GLOBALS["_CONFIG"] = array_merge($GLOBALS["_CONFIG"], $defines);

use \Summoner\Framework\Path\Router;
$url = isset($_GET["url"]) ? $_GET["url"] : $_SERVER["REQUEST_URI"];
$Router = new Router($url);
require("templates/routes.php");
echo $Router->run();