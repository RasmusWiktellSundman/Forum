<?php
namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Exceptions\InvalidUserInput;
use App\Models\Category;
use App\Models\Post;
use App\Models\Topic;

class TopicController {
    /**
     * Visar en tråd
     *
     * @param int $category_id
     * @param int $topic_id
     * @return void
     */
    public function index(int $category_id, int $topic_id): void
    {
        $topic = Topic::getById($topic_id);
        if($topic == null) {
            http_response_code(404);
            renderView('errors/404', 'base');
            return;
        }

        renderView('topic/topic', 'base', [
            'topic' => $topic,
            'posts' => $topic->getPosts()
        ]);
    }

    /**
     * Visar sidan för att skapa ny tråd
     *
     * @return void
     */
    public function create()
    {
        // Kollar om användaren är inloggad
        if(!Auth::isLoggedIn()) {
            http_response_code(403);
            renderView('errors/403', 'base');
            return;
        }

        // Hämtar kategori som inlägget ska skapas under (som standard)
        $category = null;
        if(isset($_GET['category'])) {
            $category = Category::getById($_GET['category']);
        }

        renderView('topic/create', 'base', [
            "categories" => Category::getAll(),
            "specifiedCategory" => $category
        ]);
    }

    /**
     * Skapar ny tråd
     *
     * @return void
     */
    public function store()
    {
        // Kollar om användaren är inloggad
        if(!Auth::isLoggedIn()) {
            http_response_code(403);
            renderView('errors/403', 'base');
            return;
        }

        try {
            // Array innehållandes godkänd användardata
            $validated = $this->validateStoreInput($_POST);
        } catch (InvalidUserInput $ex) {
            // Visa vy med errors vid ogiltig input och avbryt exekvering
            renderView('topic/create', 'base', [
                'errors' => $ex->getErrors(),
                'previous' => $ex->getValidated(),
                'categories' => Category::getAll()
            ]);
            return;
        }
        
        // Försöker skapa tråd
        $topic = Topic::create(
            $validated['title'],
            Auth::user(),
            $validated['category']
        );

        // Skapar första inlägget i tråden
        Post::create($validated['message'], Auth::user(), $topic);

        // Visar trådens egen sida
        http_response_code(301); // Moved permanently
        header('Location: '.$_ENV['BASE_URL'].'/category/'.$validated['category']->getId().'/'.$topic->getId());
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
            $errors['title'] = "Titeln är obligatoriskt";
        } else if(strlen($_POST['title']) > 45) {
            $errors['title'] = "Titeln får inte vara mer än 45 tecken";
        } else if(!preg_match('/^[\wå-öÅ-Ö _-]+$/', $_POST['title'])) {
            $errors['title'] = "Titel får endast innehålla a-z, A-Z, å-ö, Å-Ö, 0-9, -, _ och mellanrum";
        } else {
            $validated['title'] = test_input($_POST['title']);
        }

        if(!isset($input['message']) || $input['message'] == '') {
            $errors['message'] = "Meddelande är obligatoriskt";
        } else if(strlen($_POST['message']) > 4095) {
            $errors['message'] = "Meddelandet får inte vara mer än 4095 tecken";
        } else {
            $validated['message'] = test_input($_POST['message']);
        }

        $category = Category::getById($_POST['category']);
        if($category == null) {
            $errors['category'] = "Ogiltigt kategori-id";
        } else {
            $validated['category'] = $category;
        }

        if(isset($errors)) {
            throw new InvalidUserInput($errors, $validated);
        }

        return $validated;
    }
}