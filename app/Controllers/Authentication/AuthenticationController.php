<?php
namespace App\Controllers\Authentication;

use App\Lib\Auth;
use App\Lib\Exceptions\InvalidUserInput;

class AuthenticationController {
    /**
     * Visa inloggningssida
     *
     * @return void
     */
    public function index()
    {
        if(Auth::isLoggedIn()) {
            // Användaren är redan inloggad, dirigera till startsidan
            http_response_code(301); // Moved permanently
            header('Location: '.$_ENV['BASE_URL']);
        }
        renderView('auth/login', 'base');
    }

    /**
     * Logga in användare
     *
     * @return void
     */
    public function store()
    {
        try {
            // Array innehållandes godkänd användardata
            $validated = $this->validateInput($_POST);
        } catch (InvalidUserInput $ex) {
            // Visa vy med errors vid ogiltig input och avbryt exekvering
            renderView('auth/login', 'base', [
                'errors' => $ex->getErrors(),
                'previous' => $ex->getValidated()
            ]);
            return;
        }

        // Loggar in användaren med det nya kontot
        $user = Auth::login($validated['email'], $validated['password']);

        if(!$user) {
            renderView('auth/login', 'base', [
                'errors' => [
                    "login" => "E-postadressen eller lösenordet är ogiltigt"
                ],
                'previous' => [
                    'email' => $validated['email']
                ]
            ]);
            return;
        }


        // Inloggning lyckades, dirigera till startsidan
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
    private function validateInput(array $input): array
    {
        // Array innehållandes godkänd användardata
        $validated = [];

        if(!isset($input['email'])|| $input['email'] == '') {
            $errors['email'] = "E-post är obligatoriskt";
        } else if(strlen($_POST['email']) > 128) {
            $errors['email'] = "E-post får inte vara mer än 128 tecken";
        } else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Ogiltigt format på e-postaddress";
        } else {
            $validated['email'] = test_input($_POST['email']);
        }

        if(!isset($input['password']) || $input['password'] == '') {
            $errors['password'] = "Lösenord är obligatoriskt";
        } else {
            $validated['password'] = test_input($_POST['password']);
        }

        if(isset($errors)) {
            throw new InvalidUserInput($errors, $validated);
        }

        return $validated;
    }
}