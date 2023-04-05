<?php
namespace App\Controllers;

class HomeController {
    public function index()
    {
        renderView('test', 'base');
    }
}