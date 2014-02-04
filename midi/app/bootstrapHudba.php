<?php
use Nette\Application\Routers\Route,
    Nette\Application\Routers\SimpleRouter;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode(false);  // debug mode MUST NOT be enabled on production server
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.hudba.neon');
if (is_file(__DIR__ . '/config/config.local.neon')) {
  $configurator->addConfig(__DIR__ . '/config/config.local.neon');
}

$container = $configurator->createContainer();


// Setup router using mod_rewrite detection
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
	$router = $container->getService('router');
	$router[] = new Route('index.php', 'Hudba:Homepage:default', Route::ONE_WAY);
	$router[] = new Route('<presenter>/<action>[/<id>]', 'Hudba:Homepage:default');
} else {
 	$container->addService('router', new SimpleRouter('Hudba:Homepage:default'));
}

return $container;