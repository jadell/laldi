<?php
error_reporting(-1);
ini_set('html_errors', 1);
ini_set('display_errors', 1);
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

return $app;