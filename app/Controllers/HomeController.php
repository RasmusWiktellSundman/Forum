<?php
namespace App\Controllers;

use App\Models\Category;

class HomeController {
    public function index()
    {
        renderView('home', 'base', [
            "categories" => Category::getAll()
        ]);
    }
}