<?php

require_once 'config/config.php';
require_once 'database/DB.php';
require_once 'controller/AuthController.php';
require_once 'controller/HomeController.php';
require_once 'controller/MateriasController.php';
require_once 'routes/Router.php';

// Configurar el router
$router = new Router ('/practica%207'); 

$router->get('/', 'AuthController@index');
$router->get('/home', 'HomeController@index');

//rutas del controlador de materias 
$router->get('/materias', 'MateriasController@index');

$router->run();

