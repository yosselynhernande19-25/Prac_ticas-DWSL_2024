<?php
require_once 'config/config.php';
require_once 'controller/HomeController.php';
require_once 'routes/Router.php';

// Configurar el router
$router = new Router('/practica%205'); 

$router->get('/', 'HomeController@index');



$router->run();
