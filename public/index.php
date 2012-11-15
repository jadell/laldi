<?php
/**
 * This is the web UI front controller.
 *
 * It loads up the DI container, then sets up URL routes
 * to handle UI requests.
 *
 * Each route handler loads up a controller to handle
 * the request. Since the controllers are defined in
 * the DI container, the routing does not need to know
 * how to instantiate the controller.
 */
$app = require_once(__DIR__.'/../bootstrap.php');

// Retrieve user info by username
$app->get('/{name}', function ($name) use ($app) {
	$controller = new Lal\HelloController();
	return $controller->helloName($name);
})
->assert('name', '[a-zA-Z]+');

// Retrieve user info by id
$app->get('/{id}', function ($id) use ($app) {
	$controller = new Lal\HelloController();
	return $controller->helloId($id);
})
->assert('id', '\d+');


// List users
$app->get('/', function () use ($app) {
	$controller = new Lal\HelloController();
	return $controller->index();
});

$app->run();
