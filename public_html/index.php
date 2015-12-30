<?
date_default_timezone_set('Europe/Kiev');

//error_reporting (E_ALL);
//ini_set("display_startup_errors", "1");
//ini_set("display_errors", "1");

ini_set("always_populate_raw_post_data", "-1");

if (version_compare(phpversion(), '5.6.0', '<') == true) exit('PHP5.6 or above');

// Константы:
define('DS', DIRECTORY_SEPARATOR);

// Узнаём путь до файлов сайта
define('SITEPATH', realpath(dirname(__FILE__) . DS . '..' . DS) . DS);

require_once SITEPATH . 'app' . DS . 'startup.php';

$router = new Router();
$router->setPath(SITEPATH . 'controllers');
$router->delegate();