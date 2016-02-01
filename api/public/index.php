<?php

// ==============================================================================
//
// This file is part of the WelStory.
//
// Create by Welfony Support <support@welfony.com>
// Copyright (c) 2012-2014 welfony.com
//
// For the full copyright and license information, please view the LICENSE
// file that was distributed with this source code.
//
// ==============================================================================

define('DS', \DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(__DIR__ . '/../../'));
define('API_ROOT_PATH', ROOT_PATH . '/api');
define('STATIC_ROOT_PATH', ROOT_PATH . '/static');


$config = parse_ini_file(API_ROOT_PATH . '/config/application.ini', true);

error_reporting($config['php']['error_reporting']);
ini_set('display_errors', $config['php']['display_errors']);
ini_set('log_errors', $config['php']['log_errors']);
ini_set('error_log', $config['php']['error_log']);
date_default_timezone_set($config['php']['timezone']);

$loader = require ROOT_PATH . '/vendor/autoload.php';

$app = new \Slim\Slim(array(
    'debug' => $config['app']['debug'],
    'log.enabled' => $config['log']['enabled'],
    'log.level' => \Slim\Log::ERROR,
    'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter(array(
        'path' => $config['log']['path'],
        'name_format' => $config['log']['name_format'],
        'message_format' => $config['log']['message_format']
    ))
));

$app->config = $config;

$app->container->singleton('conn', function($app) {
    $dbConfig = new \Doctrine\DBAL\Configuration();
    $connectionParams = array(
        'dbname' => $app['config']['db']['dbname'],
        'user' => $app['config']['db']['user'],
        'password' => $app['config']['db']['password'],
        'host' => $app['config']['db']['host'],
        'driver' => $app['config']['db']['driver'],
        'charset' => $app['config']['db']['charset']
    );

    return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $dbConfig);
});

$app->container->singleton('passHash', function($app) {
    $adapter = new \PHPassLib\Hash\Adapter\Pbkdf2(array(
        'iterationCount' => $app['config']['passhash']['iteration_count']
    ));
    return new \PHPassLib\Hash($adapter);
});

foreach (glob($config['app']['routes_path'] . DS . '*php') as $file) {
    require_once $file;
}

$app->run();
