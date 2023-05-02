<?php
namespace App\Controllers;

use App\Lib\Auth;
use App\Lib\Exceptions\InvalidUserInput;
use App\Models\Category;
use App\Models\Post;
use App\Models\Topic;

class PostController {

    public function store(int $topic_id)
    {
        // Kollar om användaren är inloggad
        if(!Auth::isLoggedIn()) {
            http_response_code(403);
            renderView('errors/403', 'base');
            return;
        }

        $topic = Topic::getById($topic_id);
        if($topic == null) {
            http_response_code(404);
            renderView('errors/404', 'base');
            return;
        }

        try {
            // Array innehållandes godkänd användardata
            $validated = $this->validateStoreInput($_POST);
        } catch (InvalidUserInput $ex) {
            // Visa vy med errors vid ogiltig input och avbryt exekvering
            // Om tråde kunde hittas visas den med felmeddelande, annars visas en 404 sida
            if(isset($validated['topic'])) {
                renderView('topic/topic', 'base', [
                    'errors' => $ex->getErrors(),
                    'previous' => $ex->getValidated(),
                    'topic' => $topic,
                    'posts' => $topic->getPosts()
                ]);
            }
            return;
        }
        
        // Försöker skapa inlägg
        Post::create(
            $validated['message'],
            Auth::user(),
            $topic
        );

        // Dirigerar tillbaka till trådens egen sida
        http_response_code(301); // Moved permanently
        header('Location: '.$_ENV['BASE_URL'].'/category/'.$topic->getCategory()->getId().'/'.$topic->getId());
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

        if(!isset($input['message']) || $input['message'] == '') {
            $errors['username'] = "Meddelande är obligatoriskt";
        } else if(strlen($_POST['message']) > 4095) {
            $errors['message'] = "Meddelandet får inte vara mer än 4095 tecken";
        } else {
            $validated['message'] = test_input($_POST['message']);
        }

        if(isset($errors)) {
            throw new InvalidUserInput($errors, $validated);
        }

        return $validated;
    }
}