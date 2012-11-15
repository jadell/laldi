<?php
error_reporting(-1);
ini_set('html_errors', 1);
ini_set('display_errors', 1);
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

/**
 * This is a simple configuration parameter.
 * It could come from a config file, or be hard-coded,
 * like it is here.
 */
$app['data.path'] = __DIR__.'/data/hello.json';

/**
 * This creates our data source.
 * The datasource will be injected into any
 * repositories so that they will have access to
 * the data.
 */
$app['data.source'] = $app->share(function ($app) {
	return new Lal\Datasource($app['data.path']);
});

/**
 * These are a factory functions.
 * Their ONLY purpose is to create new objects,
 * and to inject their dependencies so that the
 * callers of the factory will receive usable
 * objects in return.
 *
 * If an object's constructor changes, or the
 * object gets new setup requirements later,
 * only the factory function needs to change.
 */
$app['user.factory'] = $app->protect(function () use ($app) {
	return new Lal\User($app['user.repo'], $app['comment.repo'], $app['pageview.repo']);
});
$app['comment.factory'] = $app->protect(function () use ($app) {
	return new Lal\Comment($app['user.repo']);
});

/**
 * The repositories control access into and out of
 * the datasource.
 *
 * They are injected with the data.source so that
 * they can read/write data, and also get injected
 * with factory functions so that they can
 * translate raw data.source data into objects.
 *
 * Since they uses the factories, they do not need
 * to know about an object's requirements. This is
 * a main goal of DI!
 */
$app['user.repo'] = $app->share(function ($app) {
	return new Lal\UserRepository($app['data.source'], $app['user.factory']);
});
$app['pageview.repo'] = $app->share(function ($app) {
	return new Lal\PageViewRepository($app['data.source']);
});
/**
 * Since the comment repo emits events when comments are saved
 * it makes sense to register the listeners for those events
 * when the repo is created.
 */
$app['comment.repo'] = $app->share(function ($app) {
	$app['event.emitter']->on('comment.saved', array($app['comment.log'], 'logComment'));
	return new Lal\CommentRepository($app['data.source'], $app['event.emitter'], $app['comment.factory']);
});

/**
 * Custom view renderer
 *
 * A simple library. Like the datasource, it is
 * injected with config parameters that are also
 * stored in the DI container.
 *
 * This shouldn't be a singleton, so we don't use
 * the `share` method when setting it up.
 */
$app['view.templates'] = __DIR__.'/templates/';
$app['view.renderer'] = function ($app) {
	return new Lal\View($app['view.templates']);
};

/**
 * This is a simple app controller.
 *
 * The app framework calls methods on this controller
 * to do work and format the results. This controller
 * is injected with the repositories and a custom view
 * renderer.
 *
 * Since the controller emits events when pages are viewed
 * it makes sense to register the listeners for those events
 * when the controller is created.
 */
$app['hello.controller'] = $app->share(function ($app) {
	$app['event.emitter']->on('user.viewed', array($app['pageview.repo'], 'incrementCount'));
	return new Lal\HelloController($app['user.repo'], $app['comment.repo'], $app['view.renderer'], $app['event.emitter']);
});

/**
 * Comment log listens for comment saved events
 */
$app['comment.log'] = $app->share(function () {
	return new Lal\CommentLog('/tmp/hello.log');
});

/**
 * Event manager for dispatching and emitting events
 */
$app['event.emitter'] = $app->share(function ($app) {
	return new Evenement\EventEmitter();
});


return $app;