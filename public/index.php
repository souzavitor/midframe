<?php

use MidFrame\AppConfig;
use Zend\ServiceManager\ServiceManager;

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$config = new AppConfig;
$container = new ServiceManager($config->get('dependencies'));
$container->setService('AppConfig', $config);
$container->setFactory(MidFrame\Application::class, MidFrame\Container\ApplicationFactory::class);

$app = $container->get('MidFrame\Application');
$app->run();
