<?php
namespace App\Controllers\Authentication;

use App\Lib\Auth;
use App\Lib\Exceptions\InvalidUserInput;
use App\Models\User;

class RegisterController {
    public function index()
    {
        if(Auth::isLoggedIn()) {
            // Användaren är redan inloggad, dirigera till startsidan
            http_response_code(301); // Moved permanently
            header('Location: '.$_ENV['BASE_URL']);
        }
        renderView('auth/register', 'base');
    }

    /**
     * Skapa ny användare
     * Användarens anrop ska göras med POST
     *
     * @return void
     */
    public function store()
    {
        // Validera att de båda lösenorden är samma
        if(test_input($_POST['password']) != test_input($_POST['password_confirm'])) {
            renderView('auth/register', 'base', [
                'errors' => ['password' => "De två angiva lösenorden är inte samma"],
                'previous' => $_POST
            ]);
            return;
        }

        try {
            // Försöker skapa användare, User validerar indata
            User::create(
                test_input($_POST['email']),
                test_input($_POST['username']),
                test_input($_POST['firstname']),
                test_input($_POST['lastname']),
                test_input($_POST['password']),
                false
            );
        } catch (InvalidUserInput $ex) {
            // Skapande av användare misslyckades, visar felmeddelande
            renderView('auth/register', 'base', [
                'errors' => $ex->getErrors(),
                'previous' => $_POST
            ]);
            return;
        }

        // Loggar in användaren med det nya kontot
        Auth::login(test_input($_POST['email']), test_input($_POST['password']));


        // Dirigera till startsidan
        http_response_code(301); // Moved permanently
        header('Location: '.$_ENV['BASE_URL']);
    }
}