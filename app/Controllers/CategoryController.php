<?php
namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Exceptions\DuplicateModelException;
use App\Lib\Exceptions\InvalidUserInput;
use App\Models\Category;

class CategoryController {
    /**
     * Visar lista på trådar inom en kategori
     *
     * @param int $category Id på kategori att visa
     * @return void
     */
    public function index(int $category): void
    {
        $category = Category::getById($category);
        if($category == null) {
            http_response_code(404);
            renderView('errors/404', 'base');
            return;
        }

        renderView("category", "base", [
            "category" => $category,
            "topics" => $category->getTopics()
        ]);
    }

    /**
     * Skapa en ny kategori
     *
     * @return void
     */
    public function store()
    {
        if(!Auth::isLoggedIn() || !Auth::user()->isAdmin()) {
            http_response_code(403); // Åtkomst nekad
            renderView('errors/403', 'base');
            return;
        }

        try {
            // Array innehållandes godkänd användardata
            $validated = $this->validateStoreInput($_POST);
        } catch (InvalidUserInput $ex) {
            // Visa vy med errors vid ogiltig input och avbryt exekvering
            renderView('home', 'base', [
                'categories' => Category::getAll(),
                'showCategoryModal' => true,
                'errors' => $ex->getErrors(),
                'previous' => $ex->getValidated()
            ]);
            return;
        }

        try {
            // Försöker skapa kategori
            Category::create(
                $validated['title'], 
                $validated['description'],
                $validated['show_in_nav']
            );
        } catch (DuplicateModelException $ex) {
            // Skapande av kategori misslyckades, visar felmeddelande
            renderView('home', 'base', [
                'categories' => Category::getAll(),
                'showCategoryModal' => true,
                'errors' => [$ex->getDuplicateColumn() => $ex->getMessage()],
                'previous' => $validated
            ]);
            return;
        }

        // Dirigerar tillbaka till startsidan
        http_response_code(301); // Moved permanently
        header('Location: '.$_ENV['BASE_URL']);
    }

    /**
     * Validerar användarens indata
     *
     * @param array $input Användarens indata
     * @throws InvalidUserInput Ifall angiven indata är ogiltig
     * @return array Validerad indata
     */
    private function validateStoreInput(array $input): array
    {
        // Array innehållandes godkänd användardata
        $validated = [];

        if(!isset($input['title']) || $input['title'] == '') {
            $errors['username'] = "Titeln är obligatoriskt";
        } else if(strlen($_POST['title']) > 45) {
            $errors['title'] = "Titeln får inte vara mer än 45 tecken";
        } else if(!preg_match('/^[\wå-öÅ-Ö _-]+$/', $_POST['title'])) {
            $errors['title'] = "Titel får endast innehålla a-z, A-Z, å-ö, Å-Ö, 0-9, -, _ och mellanrum";
        } else {
            $validated['title'] = test_input($_POST['title']);
        }

        if(strlen($_POST['description']) > 128) {
            $errors['description'] = "Beskrivning får inte vara mer än 128 tecken";
        } else if(isset($_POST['description']) && $_POST['description'] != "" && !preg_match('/^[\wå-öÅ-Ö _-]+$/', $_POST['description'])) {
            $errors['description'] = "Beskrivning får endast innehålla a-z, A-Z, å-ö, Å-Ö, 0-9, -, _ och mellanrum";
        } else {
            $validated['description'] = test_input($_POST['description']);
        }

        $validated['show_in_nav'] = isset($_POST['show_in_navigation']);

        if(isset($errors)) {
            throw new InvalidUserInput($errors, $validated);
        }

        return $validated;
    }
}