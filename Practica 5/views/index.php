<?php

require_once 'config/config.php';

require_once "controllers/HomeController.php";
require_once "routes/Router.php";

// Configurar el router
$router = new Router();

$router->get('/', 'HomeController@index');

$router->run();