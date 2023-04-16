<?php
declare(strict_types=1);

use App\Lib\Database;

// Läser in composers autoload fil.
require __DIR__ . '/../vendor/autoload.php';

// Sätter upp globala konstanter och läser in miljövariabler
define('VIEWS_PATH', __DIR__.'/../views');
$_ENV = parse_ini_file('../.env');

// Startar session
session_start();

// Create Router instance
$router = new \Bramus\Router\Router();

// Definerar routes
$router->setNamespace('\App\Controllers');
$router->get('/', 'HomeController@index');
$router->get('/register', 'Authentication\RegisterController@index');
$router->post('/register', 'Authentication\RegisterController@store');

// Renderar 404 sida ifall användarens angiven path inte finns
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    renderView('errors/404', 'base');
});

// Aktiverar router
$router->run();

// Stänger databasanslutning (om en upprättats)
Database::closeConnection();