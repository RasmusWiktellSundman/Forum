<?php
use \App\Controllers\HomeController;

// Läser in composers autoload fil.
require __DIR__ . '/../vendor/autoload.php';

// Sätter upp globala konstanter
define('VIEWS_PATH', __DIR__.'/../views');
$_ENV = parse_ini_file('../.env');

class Test {
    public function testing()
    {
        $test = "Hello hello";
        echo "Hello World";
        renderView('test');
    }
}

// Create Router instance
$router = new \Bramus\Router\Router();

// Definerar routes
$router->setNamespace('\App\Controllers');
$router->get('/', 'HomeController@index');

// Renderar 404 sida ifall användarens angiven path inte finns
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    renderView('errors/404');
});

// Aktiverar router
$router->run();