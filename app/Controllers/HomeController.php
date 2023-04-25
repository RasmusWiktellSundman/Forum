<?php
namespace App\Controllers;

class HomeController {
    public function index()
    {
        renderView('home', 'base');
    }
}