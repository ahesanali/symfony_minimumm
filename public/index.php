<?php

// public/index.php
require_once dirname(__DIR__).'/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

// Load .env and set env/debug values
(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');


$kernel = new Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
$kernel->boot();

// ✅ Make container globally accessible
global $container;
global $router;
$container = $kernel->getContainer();
$router = $container->get('router');


include_once __DIR__ . '/helper.php';

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

