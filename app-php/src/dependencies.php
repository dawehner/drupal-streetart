<?php
// DIC configuration

use AppPhp\JsonApiTwigExtension;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['http'] = function ($container) {
  return new \GuzzleHttp\Client();
};

$container['view'] = function ($container) {
  $view = new \Slim\Views\Twig(__DIR__ . '/../templates', [
    'debug' => TRUE,
//    'cache' => 'path/to/cache'
  ]);

  // Instantiate and add Slim specific extension
  $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Twig_Extension_Debug());
  $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
  $view->addExtension(new JsonApiTwigExtension($container['http'], 'http://localhost/drupal-streetart/web', $container['request']));

  return $view;
};
